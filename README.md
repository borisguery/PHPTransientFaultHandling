# transient-error-handling

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Provide a way to easily retry actions on transient errors / exceptions such as a network failure.

## Install

Via Composer

``` bash
$ composer require borisguery/transient-error-handling
```

## Usage

``` php
use Bgy\TransientFaultHandling\RetryStrategies\FixedInterval;
use Bgy\TransientFaultHandling\RetryPolicy;
use Bgy\TransientFaultHandling\ErrorDetectionStrategies\TransientErrorCatchAllStrategy;

$retryCount = 10;
$retryIntervalInMicroseconds = 1000000 // 1 sec 
$retryStrategy = new FixedInterval($retryCount, $retryIntervalInMicroseconds);
$errorDetectionStrategy = new TransientErrorCatchAllStrategy(); // You may want to implement your own

$retryPolicy = new RetryPolicy($errorDetectionStrategy, $retryStrategy);
$retryPolicy->execute(function() {
    // try to connect to MySQL
    throw new ConnectionError("Unable to connect to MySQL");
});

// This configuration will try to execute 10 times the function everyseconds if an exception is thrown. 
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Credits

- [Boris Guéry][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/borisguery/transient-error-handling.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/borisguery/transient-error-handling/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/borisguery/transient-error-handling.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/borisguery/transient-error-handling.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/borisguery/transient-error-handling.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/borisguery/transient-error-handling
[link-travis]: https://travis-ci.org/borisguery/transient-error-handling
[link-scrutinizer]: https://scrutinizer-ci.com/g/borisguery/transient-error-handling/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/borisguery/transient-error-handling
[link-downloads]: https://packagist.org/packages/borisguery/transient-error-handling
[link-author]: https://github.com/borisguery
[link-contributors]: ../../contributors
