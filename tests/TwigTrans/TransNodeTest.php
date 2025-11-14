<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\TextNode;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Compiler;

/**
 * @covers \jblond\TwigTrans\TransNode
 */
final class TransNodeTest extends TestCase
{
    /**
     * @covers \jblond\TwigTrans\TransNode::__construct
     * @return void
     */
    public function testConstructorWithoutPluralOrCount()
    {
        $body = new TextNode('simple text', 1);
        $node = new TransNode($body, null, null, null, 1);
        $this->assertInstanceOf(TransNode::class, $node);
        $this->assertSame($body, $node->getNode('body'));
    }

    /**
     * @covers \jblond\TwigTrans\TransNode::__construct
     * @return void
     */
    public function testConstructorWithPluralAndCount()
    {
        $body = new TextNode('counted', 1);
        $plural = new Nodes([new TextNode('plural', 1)], 1);
        $count = new ConstantExpression(2, 1);
        $notes = new TextNode('notes', 1);
        $node = new TransNode($body, $plural, $count, $notes, 1);

        $this->assertInstanceOf(TransNode::class, $node);
        $this->assertSame($body, $node->getNode('body'));
        $this->assertSame($plural, $node->getNode('plural'));
        $this->assertSame($count, $node->getNode('count'));
        $this->assertSame($notes, $node->getNode('notes'));
    }

    /**
     * @covers \jblond\TwigTrans\TransNode::compile
     * @return void
     */
    public function testCompileMethod()
    {
        // Set up a Twig environment with cache (required for some nodes)
        $env = new Environment(new ArrayLoader([]), ['cache' => sys_get_temp_dir()]);
        $compiler = new Compiler($env);

        $body = new TextNode('compilable', 1);
        $node = new TransNode($body, null, null, null, 1);

        // Just ensure no exception is thrown when compiling
        $node->compile($compiler);
        $this->assertTrue(true);
    }
}
