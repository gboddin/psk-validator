# PSK validator

## Introduction

This library allows you to authenticate signed message from a client
using an pre-shared key and salt based hash.

## Installation

```bash
composer require gboddin/psk-validator
```

## Usage

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
 * Signs a bunch of data and get the signature :
 */
$signature =  $sigValidation->sign($data, null);


/**
 * Server :
 * The optional third parameter allows to define a maximum time drift  in minutes ( default 2 minutes )
 * In this case $signature would come from the client and shared secret from DB
 */

$sigValidation = new \PSKValidator($sharedsecret, 'sha256', 3);

var_dump(
    $data,
    $signature,
    $sigValidation->getTimeBasedSignatures($data),
    $sigValidation->verify($data, $signature)
);
```