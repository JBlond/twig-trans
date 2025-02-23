<?php

namespace jblond\TwigTrans;

use Twig\Attribute\YieldReady;
use Twig\Node\Node;

/**
 * Represents a list of nodes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class Nodes extends Node
{
    public function __construct(array $nodes = [], int $lineno = 0)
    {
        parent::__construct($nodes, [], $lineno);
    }
}
