# PSK validator

## Introduction

This library allows you to authenticate signed message from a client
using an pre-shared key and salt based hash.

## Installation

```bash
composer require gboddin/psk-validator
```

## Usage

### Client
```php
$sharedsecret = '43223ff65b6ce17072cda5729b20daceec611d1f39e76040d347ceeca51d2a47';
$data = json_encode(['suff','otherstuff',['machin' => 'bidule']]);

/**
 * Client :
 * Invoke the validator with the pre-shared key and an algo  (sha256 by default) and
 * define an allowed time drift in minutes ( 2 by default ).
 */
$sigValidation = new \Gbo\PSKValidator($sharedsecret, 'sha256');
/**
 * Signs a bunch of data and get the signature.
 * The second optional parameters allows for a user provided salt instead
 * of the default time based salt. It must be agreed on between client and server.
 */
$signature =  $sigValidation->sign($data, null);
```

### Server

```php

/**
 * Server :
 * The optional third parameter allows to define a maximum time drift  in minutes ( default 2 minutes )
 */
 
$signature =  $httpRequest->getHeader('x-signature');
$sharedsecret = '43223ff65b6ce17072cda5729b20daceec611d1f39e76040d347ceeca51d2a47';
$sigValidation = new \PSKValidator($sharedsecret, 'sha256', 2);
$data = $httpRequest->getBody();

/**
 * Server :
 * The third optional parameters allows for a user provided salt instead
 * of the default time based salt. It must be agreed on between client and server.
 */

$signatureIsValid = $sigValidation->verify($data, $signature, null);

var_dump(
    $data,
    $signature,
    $sigValidation->getTimeBasedSignatures($data),
    $signatureIsValid
);

```

### Server output

```sh
string(41) "["suff","otherstuff",{"machin":"bidule"}]"
string(64) "d85a2d6873e034cb3ab8c490cb82139d8dabae6c08581cca0a2e7497ead287a4"
array(5) {
  [0]=>
  string(64) "d85a2d6873e034cb3ab8c490cb82139d8dabae6c08581cca0a2e7497ead287a4"
  [1]=>
  string(64) "dc150239c61fe272b7ca44ad0918d159a84e5bc1661db48bad04a81bc7f4c742"
  [2]=>
  string(64) "e1822fc6cc7bbf1184b29efaaaceac6d598fb406b4f8cf9b3717b3d0c533c19f"
  [3]=>
  string(64) "d85a2d6873e034cb3ab8c490cb82139d8dabae6c08581cca0a2e7497ead287a4"
  [4]=>
  string(64) "d85a2d6873e034cb3ab8c490cb82139d8dabae6c08581cca0a2e7497ead287a4"
}
bool(true)


```