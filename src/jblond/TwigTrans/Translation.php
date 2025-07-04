<?php

namespace jblond\TwigTrans;

use jblond\TwigTrans\Operators\AndOperator;
use jblond\TwigTrans\Operators\NotOperator;
use jblond\TwigTrans\Operators\OrOperator;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Class Translation
 * @package jblond\TwigTrans
 */
final class Translation implements ExtensionInterface
{
    /**
     * Local version of ExpressionParser::OPERATOR_LEFT
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
        // If empty, return original value
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
        // Without the brackets, there is no need to run the rest of this method
        if (!str_contains($string, '{{')) {
            return $string;
        }
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                return self::replaceContext($string, $value);
            }
            // Ignore objects since only simple variables can be used
            if (is_object($value)) {
                continue;
            }
            $string = str_replace('{{ ' . $key . ' }}', $value, $string);
        }
        return $string;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('Translation', [$this, 'transGetText']),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('transGetText', [$this, 'transGetText']),
        ];
    }


    /**
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [
            new TwigTest('Translation', null)
        ];
    }

    /**
     * @return TransTag[]
     */
    public function getTokenParsers(): array
    {
        return [
            new TransTag()
        ];
    }

    /**
     * @return MacroAutoImportNodeVisitor[]
     */
    public function getNodeVisitors(): array
    {
        return [new MacroAutoImportNodeVisitor()];
    }

    /**
     * @uses AndOperator
     * @uses NotOperator
     * @uses OrOperator
     *
     * @return array[]
     */
    public function getOperators(): array
    {
        return [
            [
                '!' => [
                    'precedence' => 50,
                    'class' => NotOperator::class, // Pass the class name as a string
                ],
            ],
            [
                '||' => [
                    'precedence' => 10,
                    'class' => OrOperator::class, // Pass the class name as a string
                    'associativity' => self::OPERATOR_LEFT
                ],
                '&&' => [
                    'precedence' => 15,
                    'class' => AndOperator::class, // Pass the class name as a string
                    'associativity' => self::OPERATOR_LEFT
                ],
            ],
        ];
    }
}
