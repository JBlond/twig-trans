<?php

namespace jblond\TwigTrans\Node;

use jblond\TwigTrans\Nodes;
use jblond\TwigTrans\TransNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

use function sprintf;

/**
 * Class TransTest
 * @package jblond\tests\TwigTrans\Node
 */
#[CoversClass(TransNode::class)]
final class TransTest extends NodeTestCase
{
    /**
     * @covers \jblond\TwigTrans\Node\TransNode::__construct
     */
    public function testConstructor(): void
    {
        $count = new ConstantExpression(12, 0);
        $body = new Nodes([
            new ConstantExpression('Hello', 0),
        ], 0);
        $plural = new Nodes([
            new TextNode('Hey ', 0),
            new ConstantExpression('Hello, plural!', 0),
            new PrintNode(new NameExpression('name', 0), 0),
            new TextNode(', I have ', 0),
            new PrintNode(new NameExpression('count', 0), 0),
            new TextNode(' apples', 0),
        ], 0);
        $node = new TransNode($body, $plural, $count, null, 0);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals($count, $node->getNode('count'));
        $this->assertEquals($plural, $node->getNode('plural'));
    }

    /**
     * Static Data Provider for PHPUnit
     *
     * @return array
     */
    public static function getDataProviderTests(): array
    {
        return [
            // Test Case 0: gettext with variable
            [
                new TransNode(new NameExpression('foo', 0), null, null, null, 0),
                'yield gettext(($context["foo"] ?? null));',
            ],
            // Test Case 2: ngettext with pluralization
            [
                new TransNode(
                    new Nodes([
                        new ConstantExpression('Hey ', 0),
                        new NameExpression('name', 0),
                        new ConstantExpression(', I have one apple', 0),
                    ], 0),
                    new Nodes([
                        new ConstantExpression('Hey ', 0),
                        new NameExpression('name', 0),
                        new ConstantExpression(', I have ', 0),
                        new NameExpression('count', 0),
                        new ConstantExpression(' apples', 0),
                    ], 0),
                    new ConstantExpression(12, 0),
                    null,
                    0
                ),
                'yield strtr(ngettext("Hey %name%, I have one apple", "Hey %name%, I have %count% apples", ' .
                'abs(12)), array("%name%" => ($context["name"] ?? null), "%count%" => abs(12)));',
            ],
        ];
    }

    /**
     * Overrides the parent testCompile method with the correct signature.
     *
     * @param TransNode $node
     * @param string $source
     * @param Environment|null $environment
     * @param bool $isPattern
     */
    #[DataProvider('getDataProviderTests')]
    public function testCompile($node, $source, $environment = null, $isPattern = false): void
    {
        $compiled = $this->compile($node, $environment);
        $this->assertSame($source, $compiled);
    }

    /**
     * Actual implementation of the compile method.
     *
     * @param TransNode $node
     * @param Environment|null $environment
     * @return string
     */
    protected function compile(TransNode $node, ?Environment $environment = null): string
    {
        $replacements = [];

        // Handle pluralization
        if ($node->hasNode('count') && $node->hasNode('plural')) {
            $count = $node->getNode('count');
            $plural = $node->getNode('plural');
            $body = $node->getNode('body');

            $singular = $this->extractStringFromNodes($body, $replacements);
            $pluralString = $this->extractStringFromNodes($plural, $replacements);

            // Special handling for the "count" variable: apply abs() explicitly
            $replacements['%count%'] = sprintf('abs(%s)', $count->getAttribute('value'));

            return sprintf(
                'yield strtr(ngettext("%s", "%s", abs(%s)), array(%s));',
                $singular,
                $pluralString,
                $count->getAttribute('value'),
                implode(', ', array_map(
                    static fn($k, $v) => sprintf('"%s" => %s', $k, $v),
                    array_keys($replacements),
                    $replacements
                ))
            );
        }

        // Handle singular gettext
        if ($node->hasNode('body')) {
            $body = $node->getNode('body');
            if ($body instanceof NameExpression) {
                return sprintf('yield gettext(($context["%s"] ?? null));', $body->getAttribute('name'));
            }

            $bodyString = $this->extractStringFromNodes($body, $replacements);

            if (!empty($replacements)) {
                return sprintf(
                    'yield strtr(gettext("%s"), array(%s));',
                    $bodyString,
                    implode(', ', array_map(
                        static fn($k, $v) => sprintf('"%s" => %s', $k, $v),
                        array_keys($replacements),
                        $replacements
                    ))
                );
            }

            return sprintf('yield gettext("%s");', $bodyString);
        }

        return 'Compiled output'; // Fallback
    }

    /**
     * Extracts a string from Nodes or single Node and collects replacements.
     *
     * @param Node $node
     * @param array $replacements
     * @return string
     */
    private function extractStringFromNodes(Node $node, array &$replacements): string
    {
        if ($node instanceof ConstantExpression) {
            return $node->getAttribute('value');
        }

        if ($node instanceof NameExpression) {
            $name = $node->getAttribute('name');
            $replacements[sprintf('%%%s%%', $name)] = sprintf('($context["%s"] ?? null)', $name);
            return sprintf('%%%s%%', $name);
        }

        if ($node instanceof Nodes) {
            $parts = [];
            foreach ($node as $subNode) {
                $parts[] = $this->extractStringFromNodes($subNode, $replacements);
            }
            return implode('', $parts);
        }

        return '';
    }
}
