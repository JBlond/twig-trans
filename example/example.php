<?php

use jblond\TwigTrans\Translation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

require '../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$langCode = 'de_DE';
putenv("LC_ALL=$langCode.UTF-8");
if (setlocale(LC_ALL, "$langCode.UTF-8") === false) {
    sprintf('Language Code %s not found', $langCode);
}

// set the path to the translation
bindtextdomain("Web_Content", "./locale");

// choose Domain
textdomain("Web_Content");

$twigConfig = array(
    'cache' => false,
    'debug' => true,
    'auto_reload' => true
);

$twigLoader = new FilesystemLoader('./tpl/');
$twig = new Environment($twigLoader, $twigConfig);

// this is for the filter |trans
$filter = new TwigFilter('trans', function (Environment $env, $context, $string) {
    return Translation::transGetText($string, $context);
}, ['needs_context' => true, 'needs_environment' => true]);

// load the i18n extension for using the translation tag for twig
// {% trans %}my string{% endtrans %}
$twig->addFilter($filter);
$twig->addExtension(new Translation());

try {
    $tpl = $twig->load('default.twig');
} catch (Exception $exception) {
    echo $exception->getMessage();
    die();
}

echo $tpl->render();