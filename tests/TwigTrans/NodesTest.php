<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;

/**
 *
 */
final class NodesTest extends TestCase
{
    /**
     * @covers \jblond\TwigTrans\Nodes::__construct
     * @return void
     */
    public function testNodesConstruct()
    {
        $nodes = new Nodes([], 0);
        $this->assertInstanceOf(Nodes::class, $nodes);
    }
}
