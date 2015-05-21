# CodeIgniter Simple and Secure Twig

This package provides simple Twig integration for [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) 3.0.

## Folder Structure

```
codeigniter/
└─── application/libraries/Twig.php
```

## Requirements

* PHP 5.4.0 or later
* Composer

## Installation

Install this project with Composer:

~~~
$ cd /path/to/codeigniter/
$ composer require kenjis/codeigniter-ss-twig:1.0.x@dev
~~~

Install `libraries/Twig.php` to your CodeIgniter application folder:

~~~
$ php vendor/kenjis/codeigniter-ss-twig/install.php
~~~

* Above command always overwrites exisiting files.
* You must run it at CodeIgniter project root folder.

## Usage

Load Twig library:

~~~php
$this->load->library('Twig');
~~~

Render Twig template:

~~~
$this->twig->render('welcome', $data);
~~~

Above code render `views/welcome.twig`.

### Supported CodeIgniter Helpers

* base_url
* site_url
* form_open
* form_close
* form_error
* set_value
* form_hidden
* anchor

Some helpers are added the functionality of auto-escaping for security.

### Reference

* http://twig.sensiolabs.org/documentation

## How to Run Tests

~~~
$ cd codeigniter-ss-twig
$ composer install
$ phpunit
~~~

## Other Implementations for CodeIgniter 3.0

* https://bitbucket.org/davidsosavaldes/ci-twig

## Related

* [CodeIgniter Composer Installer](https://github.com/kenjis/codeigniter-composer-installer)
* [Cli for CodeIgniter 3.0](https://github.com/kenjis/codeigniter-cli)
* [CI PHPUnit Test for CodeIgniter 3.0](https://github.com/kenjis/ci-phpunit-test)
