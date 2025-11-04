<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;

/**
 *
 */
final class MacroAutoImportNodeVisitorTest extends TestCase
{
    /**
     * @covers \jblond\TwigTrans\MacroAutoImportNodeVisitor::enterNode
     * @covers \jblond\TwigTrans\MacroAutoImportNodeVisitor::leaveNode
     * @return void
     * @throws \Twig\Error\SyntaxError
     */
    public function testEnterAndLeaveNode()
    {
        $visitor = new MacroAutoImportNodeVisitor();
        $node = $this->getMockBuilder(\Twig\Node\Node::class)->disableOriginalConstructor()->getMock();
        $this->assertInstanceOf(\Twig\Node\Node::class, $visitor->enterNode($node, new \Twig\Environment(new \Twig\Loader\ArrayLoader([]))));
        $this->assertInstanceOf(\Twig\Node\Node::class, $visitor->leaveNode($node, new \Twig\Environment(new \Twig\Loader\ArrayLoader([]))));
    }

    /**
     * @covers \jblond\TwigTrans\MacroAutoImportNodeVisitor::getPriority
     * @return void
     */
    public function testGetPriority()
    {
        $visitor = new MacroAutoImportNodeVisitor();
        $this->assertIsInt($visitor->getPriority());
    }
}
