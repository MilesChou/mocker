# Mocker for HTTP Client 

[![Build Status](https://travis-ci.com/MilesChou/mocker.svg?branch=master)](https://travis-ci.com/MilesChou/mocker)
[![codecov](https://codecov.io/gh/MilesChou/mocker/branch/master/graph/badge.svg)](https://codecov.io/gh/MilesChou/mocker)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/47fcc79753df4b1185ae85f4014c4699)](https://www.codacy.com/manual/MilesChou/mocker)
[![Latest Stable Version](https://poser.pugx.org/MilesChou/mocker/v/stable)](https://packagist.org/packages/MilesChou/mocker)
[![Total Downloads](https://poser.pugx.org/MilesChou/mocker/d/total.svg)](https://packagist.org/packages/MilesChou/mocker)
[![License](https://poser.pugx.org/MilesChou/mocker/license)](https://packagist.org/packages/MilesChou/mocker)

The mock helper for HTTP client.

## Installation

Use Composer to install.

```
composer require mileschou/mocker
```

## Usage for Guzzle Client

`MockBuilder` is a helper to build Guzzle Client with MockHandler.

```php
use MilesChou\Mocker\Guzzle\MockBuilder;

$expected = new Psr7Response();
$history = [];

$client = MockBuilder::createClient($expected, $history);
$client->get('somewhere'); // will return $expected

$history[0]['request']; // The Guzzle request
```

Sure, `MockBuilder` supports PSR-18 client, too.

```php
use MilesChou\Mocker\Guzzle\MockBuilder;

$expected = new Psr7Response();
$history = [];

$client = MockBuilder::createPsr18Client($expected, $history);
$client->sendRequest(new Psr7Request());

$history[0]['request']; // The Guzzle request
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
