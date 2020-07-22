<?php

namespace jblond\TwigTrans;

use Twig\Compiler;
use Twig\Extension\ExtensionInterface;
use Twig\NodeVisitor\MacroAutoImportNodeVisitor;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Class Translation
 * @package jblond\TwigTrans
 */
class Translation implements ExtensionInterface
{
    /**
     * Override function to have I18n
     * @param string $value
     * @param $context
     * @return string
     */
    public static function transGetText($value, $context)
    {
        $getTextString = gettext($value);
        //if empty return original value
        if (empty($getTextString)) {
            return $value;
        }
        return self::replaceContext($getTextString, $context);
    }

    /**
     * @param string $string
     * @param array $context
     * @return string
     */
    private static function replaceContext(string $string, array $context): string
    {
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                return self::replaceContext($string, $value);
            }
            $string = str_replace('{{ ' . $key . ' }}', $value, $string);
        }
        return $string;
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('Translation', 'transGetText'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('transGetText', [$this, 'transGetText']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTests()
    {
        return [
            new TwigTest('Translation', 'test')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTokenParsers()
    {
        return [
            new TransTag()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getNodeVisitors()
    {
        return [new MacroAutoImportNodeVisitor()];
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return [
            [
                '!' => [
                    'precedence' => 50,
                    'class' => new class {
                        public function operator(Compiler $compiler): Compiler
                        {
                            return $compiler->raw('!');
                        }
                    }
                ],
            ],
            [
                '||' => [
                    'precedence' => 10,
                    'class' => new class {
                        public function operator(Compiler $compiler): Compiler
                        {
                            return $compiler->raw('||');
                        }
                    },
                    'associativity' => 1
                ],
                '&&' => [
                    'precedence' => 15,
                    'class' => new class {
                        public function operator(Compiler $compiler): Compiler
                        {
                            return $compiler->raw('&&');
                        }
                    },
                    'associativity' => 1
                ],
            ],
        ];
    }

    /**
     * @return bool
     */
    public function test()
    {
        return true;
    }
}
