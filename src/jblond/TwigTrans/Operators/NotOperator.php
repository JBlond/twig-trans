<?php

namespace jblond\TwigTrans\Operators;

use Twig\Compiler;

class NotOperator
{
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('!');
    }

}
