# Twig Trans

[![Latest Version](https://img.shields.io/github/release/JBlond/twig-trans.svg?style=flat-square&label=Release)](https://github.com/JBlond/twig-trans/releases) 
[![Packagist Installs](https://badgen.net/packagist/dt/jblond/twig-trans)](https://packagist.org/packages/jblond/twig-trans)

## Introduction

This is the replacement for the old **Twig** Extensions **I18n** / **Intl** / **I18nExtension** for the translation with po / mo 
**gettext** files.

I didn't want to install Symfony, but Twig only. Symfony seemed to be too much overhead.

This extension enables Twig templates to use `|trans` and `{% trans %}` + `{% endtrans %}` again

## Install

```shell
composer require jblond/twig-trans
```

## Example Use

```PHP
<?php

use jblond\TwigTrans\Translation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

require '../vendor/autoload.php';

$langCode = 'de_DE';
putenv("LC_ALL=$langCode.UTF-8");
if (setlocale(LC_ALL, "$langCode.UTF-8") === false) {
    echo sprintf('Language Code %s not found', $langCode);
}

// set the path to the translation
bindtextdomain("Web_Content", "./locale");

// choose Domain
textdomain("Web_Content");

$twigConfig = [
    'cache' => false,
    'debug' => true,
    'auto_reload' => true
];

$twigLoader = new FilesystemLoader('./tpl/');
$twig = new Environment($twigLoader, $twigConfig);
// if you need  {{ dump() }} in the twig templates add
//$twig->addExtension(new DebugExtension());
// and use Twig\Extension\DebugExtension;  // at the top of the file

// this is for the filter |trans
$filter = new TwigFilter(
    'trans', 
    function ($context, $string) {
        return Translation::transGetText($string, $context);
    }, 
    ['needs_context' => true]
);
$twig->addFilter($filter);

// load the i18n extension for using the translation tag for twig
// {% trans %}my string{% endtrans %}
$twig->addExtension(new Translation());

try {
    $tpl = $twig->load('default.twig');
} catch (Exception $exception) {
    echo $exception->getMessage();
    die();
}

echo $tpl->render();
```


## Requirements

* PHP 7.2 or greater
* PHP Multibyte String ' gettext'
* Twig >= 3.0

### Optional Requirements

* xgettext for Extract / generating po files.

### License (MIT License)

see [License](LICENSE)

## Tests

```bash
composer run-script php_src
composer run-script php_test
composer run-script phpunit
```

## Contribution, wishes and bug

Raise an [issue](https://github.com/JBlond/twig-trans/issues)
