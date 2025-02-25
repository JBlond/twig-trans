<?php

namespace jblond\TwigTrans\Node;

use jblond\TwigTrans\Nodes;
use jblond\TwigTrans\TransNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

use function sprintf;

/**
 * Class TransTest
 * @package jblond\tests\TwigTrans\Node
 * @psalm-suppress UnusedClass
 */
final class TransTest extends NodeTestCase
{
    /**
     * @covers TransNode::__construct
     */
    public function testConstructor(): void
    {
        $count = new ConstantExpression(12, 0);
        $body = new Nodes([
            new TextNode('Hello', 0),
        ], 0);
        $plural = new Nodes([
            new TextNode('Hey ', 0),
            new PrintNode(new NameExpression('name', 0), 0),
            new TextNode(', I have ', 0),
            new PrintNode(new NameExpression('count', 0), 0),
            new TextNode(' apples', 0),
        ], 0);
        $node = new TransNode($body, $plural, $count, null, 0);

        /** @var mixed $node */
        /** @var mixed $body */
        $this->assertEquals($body, $node->getNode('body'));
        /** @var mixed $count */
        $this->assertEquals($count, $node->getNode('count'));
        /** @var mixed $plural */
        $this->assertEquals($plural, $node->getNode('plural'));
    }


    /**
     * @return array
     */
    public function getTests(): array
    {
        $tests = [];

        $body = new NameExpression('foo', 0);
        $node = new TransNode($body, null, null, null, 0);
        $tests[] = [$node, sprintf('yield gettext(%s);', NodeTestCase::createVariableGetter('foo'))];

        $body = new ConstantExpression('Hello', 0);
        $node = new TransNode($body, null, null, null, 0);
        $tests[] = [$node, 'yield gettext("Hello");'];

        $body = new Nodes([
            new TextNode('Hello', 0),
        ], 0);
        $node = new TransNode($body, null, null, null, 0);
        $tests[] = [$node, 'yield gettext("Hello");'];

        $body = new Nodes([
            new TextNode('J\'ai ', 0),
            new PrintNode(new NameExpression('foo', 0), 0),
            new TextNode(' pommes', 0),
        ], 0);
        $node = new TransNode($body, null, null, null, 0);
        $tests[] = [
            $node,
            sprintf(
                'yield strtr(gettext("J\'ai %%foo%% pommes"), array("%%foo%%" => %s, ));',
                NodeTestCase::createVariableGetter('foo')
            )
        ];

        $count = new ConstantExpression(12, 0);
        $body = new Nodes([
            new TextNode('Hey ', 0),
            new PrintNode(new NameExpression('name', 0), 0),
            new TextNode(', I have one apple', 0),
        ], 0);
        $plural = new Nodes([
            new TextNode('Hey ', 0),
            new PrintNode(new NameExpression('name', 0), 0),
            new TextNode(', I have ', 0),
            new PrintNode(new NameExpression('count', 0), 0),
            new TextNode(' apples', 0),
        ], 0);
        $node = new TransNode($body, $plural, $count, null, 0);
        $tests[] = [
            $node,
            sprintf(
                'yield strtr(ngettext("Hey %%name%%, I have one apple", "Hey %%name%%, I have %%count%% apples", ' .
                'abs(12)), array("%%name%%" => %s, "%%name%%" => %s, "%%count%%" => abs(12), ));',
                NodeTestCase::createVariableGetter('name'),
                NodeTestCase::createVariableGetter('name')
            )
        ];

        // with escaper extension set to on
        $body = new Nodes([
            new TextNode('J\'ai ', 0),
            new PrintNode(
                new FilterExpression(
                    new NameExpression('foo', 0),
                    new ConstantExpression('escape', 0),
                    new Nodes(),
                    0
                ),
                0
            ),
            new TextNode(' pommes', 0),
        ], 0);

        $node = new TransNode($body, null, null, null, 0);
        $tests[] = [
            $node,
            sprintf(
                'yield strtr(gettext("J\'ai %%foo%% pommes"), array("%%foo%%" => %s, ));',
                NodeTestCase::createVariableGetter('foo')
            )
        ];

        // with notes
        $body = new ConstantExpression('Hello', 0);
        $notes = new TextNode('Notes for translators', 0);
        $node = new TransNode($body, null, null, $notes, 0);
        $tests[] = [$node, "// notes: Notes for translators\nyield gettext(\"Hello\");"];

        $body = new ConstantExpression('Hello', 0);
        $notes = new TextNode("Notes for translators\nand line breaks", 0);
        $node = new TransNode($body, null, null, $notes, 0);
        $tests[] = [$node, "// notes: Notes for translators and line breaks\nyield gettext(\"Hello\");"];

        $count = new ConstantExpression(5, 0);
        $body = new TextNode('There is 1 pending task', 0);
        $plural = new Nodes([
            new TextNode('There are ', 0),
            new PrintNode(new NameExpression('count', 0), 0),
            new TextNode(' pending tasks', 0),
        ], 0);
        $notes = new TextNode('Notes for translators', 0);
        $node = new TransNode($body, $plural, $count, $notes, 0);
        $tests[] = [
            $node,
            "// notes: Notes for translators\n" .
            'yield strtr(ngettext("There is 1 pending task", "There are %count% pending tasks", abs(5)),' .
            ' array("%count%" => abs(5), ));'
        ];

        return $tests;
    }
}
