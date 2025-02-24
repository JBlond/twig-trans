<?php

namespace jblond\TwigTrans;

use Twig\Attribute\YieldReady;
use Twig\Node\Node;

/**
 * Class Nodes
 * @package jblond\TwigTrans
 */
#[YieldReady]
class Nodes extends Node
{
    /**
     * @param array $nodes
     * @param int $lineno
     */
    public function __construct(array $nodes = [], int $lineno = 0)
    {
        parent::__construct($nodes, [], $lineno);
    }
}
