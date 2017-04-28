<?php
namespace Gbo;

/**
 * Class PSKValidator
 *
 * Pre-shared key validator :
 *
 * Give a shared private key to your client and validate signed data
 * with a time based window.
 *
 */
class PSKValidator
{

    /**
     * @var string $sharedsecret Secret shared between client and server
     *
     */
    private $sharedsecret;
    /**
     * @var string $hash_type a hashing algo supported by hash_algos()
     */
    private $hash_type;
    /**
     * @var int $slack Allowed time drift ( in minutes ) to compensate for client/server time differences
     */
    private $slack;

    /**
     * Salt and time based HMACAuthenticator constructor.
     *
     * @param string $sharedsecret Shared secret between server and client
     * @param string $hash_type Hashing algo
     * @param int $timeDrift Maximum time drift ( default 2 )
     */
    public function __construct($sharedsecret, $hash_type = 'sha256', $slack = 2)
    {
        $this->sharedsecret = $sharedsecret;
        $this->hash_type = $hash_type;
        $this->slack = $slack;
    }

    /**
     * Sign a message with a seed
     *
     * @param $data string The message to sign
     * @param null|string $salt The salt to use, by default time-based
     * @return string
     */
    public function sign($data, $salt = null)
    {
        /**
         * A static seed is not provided, use time based seed
         */
        if ($salt === null) {
            $salt = gmdate("YmdHi");
        }
        return hash_hmac($this->hash_type, $salt.$data, $this->sharedsecret);
    }


    /**
     * Verify a signed messaged
     *
     * @param $data string The message to verify
     * @param $hash string The hash to verify against (client)
     * @param null|string $salt The salt to use, by default time-based
     * @return bool
     */
    public function verify($data, $hash, $salt = null)
    {
        /**
         * If a static seed is provided, use it
         */
        if ($salt !== null) {
            return $this->sign($data, $salt) == $hash;
        }
        /**
         * Else compare against time based hashes
         */
        return in_array($hash, $this->getTimeBasedSignatures($data));
    }

    /**
     * Returns time based signatures for data
     *
     * @param $data string The message to verify
     * @return array
     */
    public function getTimeBasedSignatures($data)
    {
        /**
         * Get "now" hash
         */
        $sigs = [
            $this->sign($data, gmdate("YmdHi"))
        ];
        /**
         * Create a range and compute hashed for - and + x minutes
         */
        foreach (range(1, $this->slack) as $slackIndex) {
            $sigs[] = $this->sign($data, gmdate("YmdHi", strtotime('+'.$slackIndex.' minutes')));
            $sigs[] = $this->sign($data, gmdate("YmdHi", strtotime('-'.$slackIndex.' minutes')));
        }
        return $sigs;
    }
}
