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
     * local version of ExpressionParser::OPERATOR_LEFT
     */
    protected const OPERATOR_LEFT = 1;

    /**
     * Override function to have I18n
     * @param string $value
     * @param array $context
     * @return string
     */
    public static function transGetText(string $value, array $context): string
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
        // Without the brackets there is no need to run the rest of this method.
        if (mb_strpos($string, '{{') === false) {
            return $string;
        }
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                return self::replaceContext($string, $value);
            }
            // Ignore objects, since only simple variables can be used
            if (is_object($value)) {
                continue;
            }
            $string = str_replace('{{ ' . $key . ' }}', $value, $string);
        }
        return $string;
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('Translation', [$this, 'transGetText']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('transGetText', [$this, 'transGetText']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTests(): array
    {
        return [
            new TwigTest('Translation', null)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTokenParsers(): array
    {
        return [
            new TransTag()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getNodeVisitors(): array
    {
        return [new MacroAutoImportNodeVisitor()];
    }

    /**
     * @inheritDoc
     */
    public function getOperators(): array
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
                    'associativity' => self::OPERATOR_LEFT
                ],
                '&&' => [
                    'precedence' => 15,
                    'class' => new class {
                        public function operator(Compiler $compiler): Compiler
                        {
                            return $compiler->raw('&&');
                        }
                    },
                    'associativity' => self::OPERATOR_LEFT
                ],
            ],
        ];
    }
}
