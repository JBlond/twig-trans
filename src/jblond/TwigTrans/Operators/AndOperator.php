<?php

namespace jblond\TwigTrans\Operators;

use jblond\TwigTrans\Translation;
use Twig\Compiler;

/**
 * AND Operator
 */
class AndOperator
{
    /**
     * @used-by Translation::getOperators()
     * @param Compiler $compiler
     * @return Compiler
     */
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('&&');
    }
}
