<?php

use jblond\TwigTrans\Translation;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

require '../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$langCode = 'de_DE';
putenv("LC_ALL=$langCode.UTF-8");
if (setlocale(LC_ALL, "$langCode.UTF-8") === false) {
    echo sprintf('Language Code %s not found', $langCode);
}

/**
 * set the path to the translation
 * @psalm-suppress UnusedFunctionCall
 */
bindtextdomain("Web_Content", "./locale");

/**
 * choose Domain
 * @psalm-suppress UnusedFunctionCall
 */
textdomain("Web_Content");

$twigConfig = [
    'cache' => './cache',
    'debug' => true,
    'auto_reload' => true
];

$twigLoader = new FilesystemLoader('./tpl/');
$twig = new Environment($twigLoader, $twigConfig);

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
$twig->addExtension(new DebugExtension());

try {
    $tpl = $twig->load('debug.twig');
} catch (Exception $exception) {
    echo $exception->getMessage();
    die();
}

echo $tpl->render(['name' => 'James']);
