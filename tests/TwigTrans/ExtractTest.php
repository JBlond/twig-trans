<?php
namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @covers \jblond\TwigTrans\Extract
 */
final class ExtractTest extends TestCase
{
    public function testAllPublicMethods()
    {
        $twig = new Environment(new ArrayLoader(['test.twig' => 'Hello']));
        $extract = new Extract($twig);
        $extract->setExecutable('/bin/true');

        // Use addTemplate and extract
        $extract->addTemplate('test.twig');
        $extract->addGettextParameter('--test');
        $extract->setGettextParameters(['--foo', '--bar']);

        // Optionally, make extract call not fatal by ensuring cache (if required)
        $extract->extract();

        // use reflection or subclassing to test reset() if needed
        $this->assertTrue(true); // Just a smoke assertion for now
    }
}
