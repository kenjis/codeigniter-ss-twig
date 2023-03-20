# CodeIgniter Simple and Secure Twig

[![Latest Stable Version](https://poser.pugx.org/kenjis/codeigniter-ss-twig/v/stable)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![Total Downloads](https://poser.pugx.org/kenjis/codeigniter-ss-twig/downloads)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![Latest Unstable Version](https://poser.pugx.org/kenjis/codeigniter-ss-twig/v/unstable)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![License](https://poser.pugx.org/kenjis/codeigniter-ss-twig/license)](https://packagist.org/packages/kenjis/codeigniter-ss-twig)

This package provides simple Twig integration for [CodeIgniter](https://github.com/codeigniter4/CodeIgniter4) 4.x.

If you use CodeIgniter3, check [master](https://github.com/kenjis/codeigniter-ss-twig/tree/master) branch.

## Requirements

* PHP 7.4 or later
* CodeIgniter 4.2.11 or later
* Twig 3.4.3 or later

## Installation

### With Composer

~~~
$ cd /path/to/codeigniter/
$ composer require kenjis/codeigniter-ss-twig
~~~

## Usage

### Loading Twig Library

~~~php
$this->twig = new \Kenjis\CI4Twig\Twig();
~~~

You can override the default configuration:

~~~php
$config = [
    'paths' => ['/path/to/twig/templates', VIEWPATH],
    'cache' => '/path/to/twig/cache',
];
$this->twig = new \Kenjis\CI4Twig\Twig($config);
~~~

### Rendering Templates

Render Twig template and output to browser:

~~~php
$this->twig->display('welcome', $data);
~~~

The above code renders `Views/welcome.twig`.

Render Twig template:

~~~php
$output = $this->twig->render('welcome', $data);
~~~

The above code renders `Views/welcome.twig`.

### Adding a Global Variable

~~~php
$this->twig->addGlobal('sitename', 'My Awesome Site');
~~~

### Getting Twig\Environment Instance

~~~php
$twig = $this->twig->getTwig();
~~~

### Supported CodeIgniter Helpers

* `base_url()`
* `site_url()`
* `anchor()`
* `form_open()`
* `form_close()`
* `form_error()`
* `form_hidden()`
* `set_value()`
* `csrf_field()`
* `validation_list_errors()`

Some helpers are added the functionality of auto-escaping for security.

> **Warning**
> `validation_list_errors()` shows Validation Errors by `Services::validation()->listErrors()`,
> and if you use user input for Validation Error messages, attackers may do XSS.
> In such a case, validate user input and escape it by yourself.

### Adding Your Functions & Filters

You can add your functions and filters with configuration:

~~~php
$config = [
    'functions'      => ['my_helper'],
    'functions_safe' => ['my_safe_helper'],
    'filters'        => ['my_filter'],
];
$this->twig = new \Kenjis\CI4Twig\Twig($config);
~~~

If your function explicitly outputs HTML code, you want the raw output to be printed.
In such a case, use `functions_safe`, and **you have to make sure the output of
the function is XSS free**.

### References

#### Documentation

* https://twig.symfony.com/doc/3.x/

#### Samples

* https://github.com/kenjis/ci4-tettei-apps (Japanese)

@TODO

* https://github.com/kenjis/codeigniter-twig-samples

## How to Run Tests

~~~
$ cd codeigniter-ss-twig
$ composer install
$ vendor/bin/phpunit
~~~

## Related Projects for CodeIgniter 4.x

### Libraries

- [CodeIgniter 3 to 4 Upgrade Helper](https://github.com/kenjis/ci3-to-4-upgrade-helper)
- [CodeIgniter3-like Captcha](https://github.com/kenjis/ci3-like-captcha)
- [PHPUnit Helper](https://github.com/kenjis/phpunit-helper)
- [CodeIgniter4 Attribute Routes](https://github.com/kenjis/ci4-attribute-routes)
- [CodeIgniter Simple and Secure Twig](https://github.com/kenjis/codeigniter-ss-twig)
- [CodeIgniter4 Viewi Demo](https://github.com/kenjis/ci4-viewi-demo)

### Tutorials

- [CodeIgniter 4 News Tutorial](https://github.com/kenjis/ci4-news)
- [CodeIgniter 4 Validation Tutorial](https://github.com/kenjis/ci4-validation-tutorial)
- [CodeIgniter4 Code Modules Test](https://github.com/kenjis/ci4-modules-test)
- [CodeIgniter 4 File Upload](https://github.com/kenjis/ci4-file-upload)

### Building Development Environment

- [CodeIgniter4 Application Template](https://github.com/kenjis/ci4-app-template)
- [CodeIgniter4 Composer Installer](https://github.com/kenjis/ci4-composer-installer)
- [docker-codeigniter-apache](https://github.com/kenjis/docker-codeigniter-apache)
