<?php

namespace jblond\TwigTrans\Operators;

use Twig\Compiler;

class AndOperator
{
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('&&');
    }
}
