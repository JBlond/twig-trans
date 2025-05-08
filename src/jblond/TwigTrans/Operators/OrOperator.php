<?php

namespace jblond\TwigTrans\Operators;

use Twig\Compiler;

/**
 * OR Operator
 */
class OrOperator
{
    /**
     * @used-by Translation::getOperators()
     * @param Compiler $compiler
     * @return Compiler
     */
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('||');
    }
}
