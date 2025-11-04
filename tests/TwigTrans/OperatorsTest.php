<?php

namespace jblond\TwigTrans;

use PHPUnit\Framework\TestCase;
use jblond\TwigTrans\Operators\AndOperator;
use jblond\TwigTrans\Operators\OrOperator;
use jblond\TwigTrans\Operators\NotOperator;
use Twig\Compiler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 *
 */
final class OperatorsTest extends TestCase
{
    /**
     * @covers \jblond\TwigTrans\Operators\AndOperator::operator
     * @return void
     */
    public function testAndOperator()
    {
        $env = new Environment(new ArrayLoader([]));
        $compiler = new Compiler($env);
        $operator = new AndOperator();
        // Just call & assert type
        $result = $operator->operator($compiler);
        $this->assertInstanceOf(Compiler::class, $result);
    }

    /**
     * @covers \jblond\TwigTrans\Operators\OrOperator::operator
     * @return void
     */
    public function testOrOperator()
    {
        $env = new Environment(new ArrayLoader([]));
        $compiler = new Compiler($env);
        $operator = new OrOperator();
        $result = $operator->operator($compiler);
        $this->assertInstanceOf(Compiler::class, $result);
    }

    /**
     * @covers \jblond\TwigTrans\Operators\NotOperator::operator
     * @return void
     */
    public function testNotOperator()
    {
        $env = new Environment(new ArrayLoader([]));
        $compiler = new Compiler($env);
        $operator = new NotOperator();
        $result = $operator->operator($compiler);
        $this->assertInstanceOf(Compiler::class, $result);
    }
}
