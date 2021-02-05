# CodeIgniter Simple and Secure Twig

[![Latest Stable Version](https://poser.pugx.org/kenjis/codeigniter-ss-twig/v/stable)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![Total Downloads](https://poser.pugx.org/kenjis/codeigniter-ss-twig/downloads)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![Latest Unstable Version](https://poser.pugx.org/kenjis/codeigniter-ss-twig/v/unstable)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![License](https://poser.pugx.org/kenjis/codeigniter-ss-twig/license)](https://packagist.org/packages/kenjis/codeigniter-ss-twig)

This package provides simple Twig integration for [CodeIgniter](https://github.com/codeigniter4/CodeIgniter4) 4.x.

If you use CodeIgniter3, check [master](https://github.com/kenjis/codeigniter-ss-twig/tree/master) branch.

## Requirements

* PHP 7.2 or later
* CodeIgniter 4.0.4 or later
* Twig 3.1.1 or later

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

* `base_url`
* `site_url`
* `anchor`
* `form_open`
* `form_close`
* `form_error`
* `form_hidden`
* `set_value`

Some helpers are added the functionality of auto-escaping for security.

### Adding Your Functions

You can add your functions with configuration:

~~~php
$config = [
	'functions' => ['my_helper'],
	'functions_safe' => ['my_safe_helper'],
];
$this->twig = new \Kenjis\CI4Twig\Twig($config);
~~~

If your function explicitly outputs HTML code, you will want the raw output to be printed. In such a case, use `functions_safe`, and **you have to make sure the output of the function is XSS free**.

### References

#### Documentation

* https://twig.symfony.com/doc/3.x/

#### Samples

@TODO

* https://github.com/kenjis/codeigniter-twig-samples
* https://github.com/kenjis/codeigniter-tettei-apps

## How to Run Tests

~~~
$ cd codeigniter-ss-twig
$ composer install
$ vendor/bin/phpunit
~~~

## Related Projects for CodeIgniter 4.x

- [CodeIgniter4 Application Template](https://github.com/kenjis/ci4-app-template)
- [CodeIgniter3-like Captcha](https://github.com/kenjis/ci3-like-captcha)
