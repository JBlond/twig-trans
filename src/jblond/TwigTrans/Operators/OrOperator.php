<?php

namespace jblond\TwigTrans\Operators;

use Twig\Compiler;

class OrOperator
{
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('||');
    }
}
