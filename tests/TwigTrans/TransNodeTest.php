<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\TextNode;

/**
 *
 */
final class TransNodeTest extends TestCase
{
    /**
     * @covers \jblond\TwigTrans\TransNode::__construct
     * @covers \jblond\TwigTrans\TransNode::compile
     * @return void
     */
    public function testConstructAndCompile()
    {
        $body = new Nodes([new TextNode('foo', 0)], 0);
        $plural = new Nodes([new TextNode('bar', 0)], 0);
        $count = new ConstantExpression(1, 0);
        $notes = new TextNode('note', 0);

        $node = new TransNode($body, $plural, $count, $notes, 1);
        $this->assertInstanceOf(TransNode::class, $node);
    }
}
