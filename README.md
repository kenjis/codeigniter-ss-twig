# CodeIgniter Simple and Secure Twig

[![Latest Stable Version](https://poser.pugx.org/kenjis/codeigniter-ss-twig/v/stable)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![Total Downloads](https://poser.pugx.org/kenjis/codeigniter-ss-twig/downloads)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![Latest Unstable Version](https://poser.pugx.org/kenjis/codeigniter-ss-twig/v/unstable)](https://packagist.org/packages/kenjis/codeigniter-ss-twig) [![License](https://poser.pugx.org/kenjis/codeigniter-ss-twig/license)](https://packagist.org/packages/kenjis/codeigniter-ss-twig)

This package provides simple Twig integration for [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) 3.0.

## Folder Structure

```
codeigniter/
└── application/
    └── libraries/
        └── Twig.php
```

## Requirements

* PHP 5.4.0 or later

## Installation

### With Composer

~~~
$ cd /path/to/codeigniter/
$ composer require kenjis/codeigniter-ss-twig
~~~

Install `libraries/Twig.php` to your CodeIgniter application folder:

~~~
$ php vendor/kenjis/codeigniter-ss-twig/install.php
~~~

* Above command always overwrites exisiting files.
* You must run it at CodeIgniter project root folder.

### Without Composer

Download the latest Twig v1.x: https://github.com/twigphp/Twig/releases

Unzip and install to `application/third_party` folder.

Download the latest codeigniter-ss-twig: https://github.com/kenjis/codeigniter-ss-twig/releases

Unzip and copy `codeigniter-ss-twig/libraries/Twig.php` to `application/libraries` folder.

Remove comment marks below and fix the path for `Autoloader.php`:

~~~diff
--- a/libraries/Twig.php
+++ b/libraries/Twig.php
@@ -9,10 +9,8 @@
  */
 
 // If you don't use Composer, uncomment below
-/*
 require_once APPPATH . 'third_party/Twig-1.xx.x/lib/Twig/Autoloader.php';
 Twig_Autoloader::register();
-*/
 
 class Twig
 {
~~~

## Usage

### Loading Twig Library

~~~php
$this->load->library('twig');
~~~

You can override the default configuration:

~~~php
$config = [
	'paths' => ['/path/to/twig/templates', VIEWPATH],
	'cache' => '/path/to/twig/cache',
];
$this->load->library('twig', $config);
~~~

### Rendering Templates

Render Twig template and output to browser:

~~~php
$this->twig->display('welcome', $data);
~~~

Above code renders `views/welcome.twig`.

> **Note:** I've changed the method name from `render()` to `display()`. Now `render()` method returns string only.

Render Twig template:

~~~php
$output = $this->twig->render('welcome', $data);
~~~

Above code renders `views/welcome.twig`.

### Adding a Global Variable

~~~php
$this->twig->addGlobal('sitename', 'My Awesome Site');
~~~

### Getting Twig_Environment Instance

~~~php
$twig = $this->twig->getTwig();
~~~

### Supported CodeIgniter Helpers

* `base_url`
* `site_url`
* `form_open`
* `form_close`
* `form_error`
* `set_value`
* `form_hidden`
* `anchor`

Some helpers are added the functionality of auto-escaping for security.

### Adding Your Functions

You can add your functions with configuration:

~~~php
$config = [
	'functions' => ['my_helper'],
	'functions_safe' => ['my_safe_helper'],
];
$this->load->library('twig', $config);
~~~

If your function explicitly outputs HTML code, you will want the raw output to be printed. In such a case, use `functions_safe`, and you have to make sure the output of the function is XSS free.

### Reference

#### Documentation

* http://twig.sensiolabs.org/documentation

#### Samples

* https://github.com/kenjis/codeigniter-twig-samples
* https://github.com/kenjis/codeigniter-tettei-apps

## How to Run Tests

~~~
$ cd codeigniter-ss-twig
$ composer install
$ phpunit
~~~

## Other Twig Implementations for CodeIgniter 3.0

* https://github.com/davidsosavaldes/Attire

## Related Projects for CodeIgniter 3.0

* [CodeIgniter Composer Installer](https://github.com/kenjis/codeigniter-composer-installer)
* [Cli for CodeIgniter 3.0](https://github.com/kenjis/codeigniter-cli)
* [CI PHPUnit Test](https://github.com/kenjis/ci-phpunit-test)
* [CodeIgniter Doctrine](https://github.com/kenjis/codeigniter-doctrine)
* [CodeIgniter Deployer](https://github.com/kenjis/codeigniter-deployer)
* [CodeIgniter3 Filename Checker](https://github.com/kenjis/codeigniter3-filename-checker)
