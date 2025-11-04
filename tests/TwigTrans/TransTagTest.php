<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;

/**
 *
 */
final class TransTagTest extends TestCase
{
    /**
     * @covers \jblond\TwigTrans\TransTag::getTag
     * @return void
     */
    public function testGetTag()
    {
        $tag = new TransTag();
        $this->assertEquals('trans', $tag->getTag());
    }
}
