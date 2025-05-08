<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;
use Twig\TwigTest;
use jblond\TwigTrans\Operators\NotOperator;

/**
 *
 */
final class TranslationTest extends TestCase
{
    /**
     * @var Translation
     */
    private Translation $translation;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->translation = new Translation();
    }

    /**
     * @return void
     */
    public function testGetFunctions(): void
    {
        $functions = $this->translation->getFunctions();
        $this->assertNotEmpty($functions);
        $this->assertInstanceOf(TwigFunction::class, $functions[0]);
        $this->assertEquals('transGetText', $functions[0]->getName());
    }

    /**
     * @return void
     */
    public function testGetTests(): void
    {
        $tests = $this->translation->getTests();
        $this->assertNotEmpty($tests);
        $this->assertInstanceOf(TwigTest::class, $tests[0]);
        $this->assertEquals('Translation', $tests[0]->getName());
    }

    /**
     * @return void
     */
    public function testGetTokenParsers(): void
    {
        $tokenParsers = $this->translation->getTokenParsers();
        $this->assertNotEmpty($tokenParsers);
        $this->assertInstanceOf(TransTag::class, $tokenParsers[0]);
    }

    /**
     * @return void
     */
    public function testGetNodeVisitors(): void
    {
        $nodeVisitors = $this->translation->getNodeVisitors();
        $this->assertNotEmpty($nodeVisitors);
        $this->assertInstanceOf(MacroAutoImportNodeVisitor::class, $nodeVisitors[0]);
    }

    /**
     * @return void
     */
    public function testGetOperators(): void
    {
        $operators = $this->translation->getOperators();
        $this->assertNotEmpty($operators);
        $this->assertArrayHasKey('!', $operators[0]);
        $this->assertEquals(50, $operators[0]['!']['precedence']);
        $this->assertEquals(NotOperator::class, $operators[0]['!']['class']);
    }
}
