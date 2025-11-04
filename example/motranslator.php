<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use jblond\TwigTrans\Translation;
use PhpMyAdmin\MoTranslator\Loader as MoLoader;

// Fehlerausgabe wie im Originalbeispiel
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Sprache (Locale) setzen
$langCode = 'de_DE';

// MoTranslator: Locale + Domain + Pfad konfigurieren
$mo = new MoLoader();
$mo->setlocale($langCode);
$mo->bindtextdomain('Web_Content', __DIR__ . '/locale');   // erwartet locale/de_DE/LC_MESSAGES/Web_Content.mo
$mo->textdomain('Web_Content');

$langCode = 'de_DE';
putenv("LC_ALL=$langCode.UTF-8");
if (setlocale(LC_ALL, "$langCode.UTF-8") === false) {
    echo sprintf('Language Code %s not found', $langCode);
}


$twigConfig = [
    'cache'       => false,
    'debug'       => true,
    'auto_reload' => true,
];
$twigLoader = new FilesystemLoader(__DIR__ . '/tpl/');
$twig = new Environment($twigLoader, $twigConfig);


$translator = $mo->getTranslator('Web_Content');


$filter = new TwigFilter(
    'trans',
    function ($context, $string) use ($translator) {
        return $translator->gettext($string);
    },
    ['needs_context' => true]
);
$twig->addFilter($filter);


$twig->addExtension(new Translation());

try {
    $tpl = $twig->load('default.twig');
} catch (Exception $exception) {
    echo $exception->getMessage();
    exit;
}

echo $tpl->render([
    'name'  => 'James',
    'count' => 3,
]);
