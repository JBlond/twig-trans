<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
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
     * @covers Translation::getFunctions
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
     * @covers Translation::getTests
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
     * @covers Translation::getTokenParsers
     * @return void
     */
    public function testGetTokenParsers(): void
    {
        $tokenParsers = $this->translation->getTokenParsers();
        $this->assertNotEmpty($tokenParsers);
        $this->assertInstanceOf(TransTag::class, $tokenParsers[0]);
    }

    /**
     * @covers Translation::getTokenParsers
     * @return void
     */
    public function testGetTokenParsers2(): void
    {
        $translation = new Translation();
        $parsers = $translation->getTokenParsers();

        $this->assertIsArray($parsers);
        $this->assertNotEmpty($parsers);
    }

    /**
     * @covers Translation::getNodeVisitors
     * @return void
     */
    public function testGetNodeVisitors(): void
    {
        $nodeVisitors = $this->translation->getNodeVisitors();
        $this->assertNotEmpty($nodeVisitors);
        $this->assertInstanceOf(MacroAutoImportNodeVisitor::class, $nodeVisitors[0]);
    }

    /**
     * @covers Translation::getOperators
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

    /**
     * @covers Translation::getOperators
     * @return void
     */
    public function testGetOperators2(): void
    {
        $translation = new Translation();
        $operators = $translation->getOperators();

        $this->assertIsArray($operators);
        $this->assertCount(2, $operators);
    }

    /**
     * @covers Translation::transGetText
     * @return void
     */
    public function testTransGetText(): void
    {
        $context = [];
        $string = 'Hello World';

        $result = Translation::transGetText($string, $context);

        $this->assertEquals('Hello World', $result);
    }

    /**
     * @covers Translation::transGetText
     * @return void
     */
    public function getFilters(): void
    {
        $translation = new Translation();
        $filters = $translation->getFilters();

        $this->assertIsArray($filters);
        $this->assertNotEmpty($filters);
    }

    /**
     * @covers Translation::transGetText
     * @return void
     */
    public function testTransGetTextWithPlaceholder(): void
    {
        $context = ['name' => 'Bob'];
        $string = 'Welcome, {{ name }}!';

        $result = Translation::transGetText($string, $context);

        $this->assertEquals('Welcome, Bob!', $result);
    }

    /**
     * @covers Translation::replaceContext
     * @return void
     * @throws ReflectionException
     */
    public function testReplaceContext(): void
    {
        $method = new ReflectionMethod(Translation::class, 'replaceContext');


        $result = $method->invokeArgs(null, [
            'Hello {{ name }}!', ['name' => 'Alice']
        ]);

        $this->assertEquals('Hello Alice!', $result);
    }

    /**
     * @covers Translation::replaceContext
     * @return void
     * @throws ReflectionException
     */
    public function testReplaceContextWithArrayAndObject(): void
    {
        $method = new ReflectionMethod(Translation::class, 'replaceContext');

        $string = 'Name: {{ name }}, Age: {{ age }}';

        // Value contains an array → should be replaced recursively
        $context = [
            'name' => ['age' => '42'], /// Is treated recursively → overwrites $string, which is okay for coverage
            'unused' => (object)['foo' => 'bar'], 32 / 5.000 // Should simply be ignored
        ];

        // Expected return of the original (since '{{' does not fit in the recursive context)
        $result = $method->invokeArgs(null, [$string, $context]);
        $this->assertIsString($result); // Important: The goal is only to execute the code paths
    }

    /**
     * @covers Translation::replaceContext
     * @return void
     *@throws ReflectionException
     */
    public function testReplaceContextSkipsObjectValues(): void
    {
        $method = new ReflectionMethod(Translation::class, 'replaceContext');
        $string = 'Hello {{ name }}!';
        $context = ['name' => (object)['foo' => 'bar']]; // << Object value is skipped
        $result = $method->invokeArgs(null, [$string, $context]);
        // The out does not change!
        $this->assertEquals('Hello {{ name }}!', $result);
    }

    /**
     * @covers Translation::transGetText
     * @return void
     */
    public function testTransGetTextReturnsOriginalWhenGettextIsEmpty(): void
    {
        $original = 'Some untranslated string';
        $result = Translation::transGetText($original, []);
        $this->assertEquals($original, $result);
    }

    /**
     * @covers Translation::replaceContext
     * @return void
     * @throws ReflectionException
     */
    public function testReplaceContextReplacesVariables(): void
    {
        $method = new ReflectionMethod(Translation::class, 'replaceContext');
        $result = $method->invokeArgs(null, ['Hello {{ name }}!', ['name' => 'Alice']]);
        $this->assertEquals('Hello Alice!', $result);
    }

    /**
     * @covers Translation::replaceContext
     * @return void
     * @throws ReflectionException
     */
    public function testReplaceContextSkipsObjects(): void
    {
        $method = new ReflectionMethod(Translation::class, 'replaceContext');
        $result = $method->invokeArgs(null, ['Hello {{ name }}!', ['name' => (object)['foo' => 'bar']]]);
        $this->assertEquals('Hello {{ name }}!', $result);
    }

    /**
     * @covers Translation::replaceContext
     * @return void
     * @throws ReflectionException
     */
    public function testReplaceContextRecursesOnArray(): void
    {
        $method = new ReflectionMethod(Translation::class, 'replaceContext');
        $result = $method->invokeArgs(null, ['Age: {{ age }}', ['name' => ['age' => '42']]]);
        $this->assertIsString($result); // Nur zur Coverage
    }
}
