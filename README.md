[![Latest Stable Version](https://img.shields.io/packagist/v/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![GitHub stars](https://img.shields.io/github/stars/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![Total Downloads](https://img.shields.io/packagist/dt/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![Build Status](https://img.shields.io/travis/drupol/phpvfs/master.svg?style=flat-square)](https://travis-ci.org/drupol/phpvfs)
 [![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/drupol/phpvfs/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/phpvfs/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/drupol/phpvfs/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/phpvfs/?branch=master)
 [![Mutation testing badge](https://badge.stryker-mutator.io/github.com/drupol/phpvfs/master)](https://stryker-mutator.github.io)
 [![License](https://img.shields.io/packagist/l/drupol/phpvfs.svg?style=flat-square)](https://packagist.org/packages/drupol/phpvfs)
 [![Say Thanks!](https://img.shields.io/badge/Say-thanks-brightgreen.svg?style=flat-square)](https://saythanks.io/to/drupol)
 [![Donate!](https://img.shields.io/badge/Donate-Paypal-brightgreen.svg?style=flat-square)](https://paypal.me/drupol)

# PHPVfs

## Description

An implementation of virtual file system and its stream wrapper in PHP.

## Requirements

* PHP >= 7.1

## Installation

```composer require drupol/phpvfs```

## Usage

### Using the default PHP stream wrapper.

```php
<?php

declare(strict_types = 1);

require_once 'vendor/autoload.php';

use drupol\phpvfs\Exporter\AttributeAscii;
use drupol\phpvfs\Filesystem\Filesystem;
use drupol\phpvfs\Exporter\GvDisplayFilesystem;
use drupol\phpvfs\StreamWrapper\PhpVfs;
use drupol\phpvfs\Node\Directory;
use drupol\launcher\Launcher;

// Create directory container.
$root = Directory::create('/');

// Create a virtual filesystem.
$vfs = new Filesystem($root);

// Register a new PHP streamwrapper (phpvfs://)
PhpVfs::register($vfs);

// Open a file handler for writing.
$file = \fopen('phpvfs://foo.txt', 'w');

// Write something.
\fwrite($file, 'bar');

// Close the file handler.
\fclose($file);

// Create a directory.
$vfs
    ->getCwd()
    ->mkdir('/a/b/c');

// Move the file.
rename('phpvfs://foo.txt', 'phpvfs://a/b/c/bar.txt');

// Get the content of the file.
$test = file_get_contents('phpvfs://a/b/c/bar.txt'); // returns 'bar'

// Export the filesystem into an ascii tree.
echo (new AttributeAscii())->export($vfs->root());

// Export the filesystem into an image and display it.
// In order to display the image, you need the package drupol/launcher
// composer require drupol/launcher
$exporter = new GvDisplayFilesystem();
Launcher::open($exporter->setFormat('svg')->export($root));
```

### Using simple directories structure

```php
<?php

declare(strict_types = 1);

require_once 'vendor/autoload.php';

use drupol\launcher\Launcher;
use drupol\phpvfs\Exporter\GvDisplayFilesystem;
use drupol\phpvfs\Node\Directory;
use drupol\phpvfs\Node\File;

$root = Directory::create('/');

$files = [
    File::create('/a/b/c/d/foo.txt', 'foo'),
    File::create('/a/b/c/d/bar.txt', 'foo'),
    File::create('/a/file_in_a_dir.txt', 'foo'),
    File::create('/a/b/file_in_b_dir.txt', 'foo'),
    File::create('/a/b/c/v/g/file_in_dir.txt', 'foo'),
    File::create('/tmp/tmp.txt', 'foo'),
];

$root->add(...$files);

// Export the filesystem into an image and display it.
// In order to display the image, you need the package drupol/launcher
// composer require drupol/launcher
$exporter = new GvDisplayFilesystem();
Launcher::open($exporter->setFormat('svg')->export($root));
```

## Objects

The current filesystem objects are
* **Filesystem**: A filesystem can contains directories and files
* **Directory**: A directory can contains directories and files.
* **File**: A file cannot contains anything.

## Code quality, tests and benchmarks

Every time changes are introduced into the library, [Travis CI](https://travis-ci.org/drupol/phpvfs/builds) run the tests and the benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/).
Feel free to check them out in the `spec` directory. Run `composer phpspec` to trigger the tests.

Before each commit some inspections are executed with [GrumPHP](https://github.com/phpro/grumphp), run `./vendor/bin/grumphp run` to check manually.

[PHPBench](https://github.com/phpbench/phpbench) is used to benchmark the library, to run the benchmarks: `composer bench`

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested, run `composer infection` to test your code.

## Contributing

Feel free to contribute to this library by sending Github pull requests. I'm quite reactive :-)
