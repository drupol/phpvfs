[![Latest Stable Version](https://img.shields.io/packagist/v/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![GitHub stars](https://img.shields.io/github/stars/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![Total Downloads](https://img.shields.io/packagist/dt/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![Build Status](https://img.shields.io/travis/drupol/phpvfs/master.svg?style=flat-square)](https://travis-ci.org/drupol/phpvfs)
 [![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/drupol/phpvfs/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/phpvfs/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/drupol/phpvfs/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/phpvfs/?branch=master)
 [![Mutation testing badge](https://badge.stryker-mutator.io/github.com/drupol/phpvfs/master)](https://stryker-mutator.github.io)
 [![License](https://img.shields.io/packagist/l/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)

# PHPVfs

## Description

An implementation of virtual file system and its stream wrapper in PHP.

## Requirements

* PHP >= 7.1

## Installation

```composer require drupol/phpvfs```

## Usage

```php
<?php

declare(strict_types = 1);

require_once 'vendor/autoload.php';

use drupol\phpvfs\Exporter\AttributeAscii;
use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\PhpVfs;

// Create a virtual filesystem.
$vfs = new Filesystem('/');

// Register a new PHP streamwrapper (phpvfs://)
PhpVfs::register($vfs);

// Open a file handler for writing.
$file = \fopen('phpvfs://a/b/c/foo.txt', 'w');

// Write something.
\fwrite($file, 'bar');

// Close the file handler.
\fclose($file);

$exporter = new AttributeAscii();
echo $exporter->export($vfs->root());
```

## Code quality, tests and benchmarks

Every time changes are introduced into the library, [Travis CI](https://travis-ci.org/drupol/phpvfs/builds) run the tests and the benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/).
Feel free to check them out in the `spec` directory. Run `composer phpspec` to trigger the tests.

Before each commit some inspections are executed with [GrumPHP](https://github.com/phpro/grumphp), run `./vendor/bin/grumphp run` to check manually.

[PHPBench](https://github.com/phpbench/phpbench) is used to benchmark the library, to run the benchmarks: `composer bench`

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested, run `composer infection` to test your code.

## Contributing

Feel free to contribute to this library by sending Github pull requests. I'm quite reactive :-)
