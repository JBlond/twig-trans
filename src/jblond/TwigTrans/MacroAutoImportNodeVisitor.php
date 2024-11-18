<?php

namespace jblond\TwigTrans;

use Twig\Environment;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\MethodCallExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ImportNode;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class MacroAutoImportNodeVisitor implements NodeVisitorInterface
{
    private $inAModule = false;
    private $hasMacroCalls = false;

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            $this->inAModule = true;
            $this->hasMacroCalls = false;
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            $this->inAModule = false;
            if ($this->hasMacroCalls) {
                $node->getNode('constructor_end')->setNode(
                    '_auto_macro_import',
                    new ImportNode(new NameExpression('_self', 0), new AssignNameExpression('_self', 0), 0, true)
                );
            }
        } elseif ($this->inAModule) {
            if (
                $node instanceof GetAttrExpression
                && $node->getNode('node') instanceof NameExpression
                && '_self' === $node->getNode('node')->getAttribute('name')
                && $node->getNode('attribute') instanceof ConstantExpression
            ) {
                $this->hasMacroCalls = true;

                $name = $node->getNode('attribute')->getAttribute('value');
                $node = new MethodCallExpression(
                    $node->getNode('node'),
                    'macro_' . $name,
                    $node->getNode('arguments'),
                    $node->getTemplateLine()
                );
                $node->setAttribute('safe', true);
            }
        }

        return $node;
    }

    public function getPriority(): int
    {
        // we must run before auto-escaping
        return -10;
    }
}
