<?php

namespace jblond\TwigTrans;

use Twig\Error\SyntaxError;
use Twig\Node\Expression\NameExpression;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Class TransTag
 * @package jblond\TwigTrans
 */
final class TransTag extends AbstractTokenParser
{
    /**
     * @param Token $token
     * @return Nodes
     * @throws SyntaxError
     */
    public function parse(Token $token): Nodes
    {
        $lineNo = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $plural = null;
        $notes = null;

        if (!$stream->test(Token::BLOCK_END_TYPE)) {
            $body = $this->parser->getExpressionParser()->parseExpression();
        } else {
            $stream->expect(Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse([$this, 'decideForFork']);
            $next = $stream->next()->getValue();

            if ('plural' === $next) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse([$this, 'decideForFork']);

                if ('notes' === $stream->next()->getValue()) {
                    $stream->expect(Token::BLOCK_END_TYPE);
                    $notes = $this->parser->subparse([$this, 'decideForEnd'], true);
                }
            } elseif ('notes' === $next) {
                $stream->expect(Token::BLOCK_END_TYPE);
                $notes = $this->parser->subparse([$this, 'decideForEnd'], true);
            }
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $this->checkTransString($body, $lineNo);

        return new TransNode($body, $plural, $count, $notes, $lineNo);
    }

    /**
     * @param Token $token
     * @return bool
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function decideForFork(Token $token): bool
    {
        return $token->test(['plural', 'notes', 'endtrans']);
    }

    /**
     * @param Token $token
     * @return bool
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function decideForEnd(Token $token): bool
    {
        return $token->test('endtrans');
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'trans';
    }

    /**
     * @param Nodes|TextNode $body
     * @param int $lineNo
     * @throws SyntaxError
     */
    protected function checkTransString($body, int $lineNo): void
    {
        foreach ($body as $node) {
            if (
                $node instanceof TextNode ||
                ($node instanceof PrintNode && $node->getNode('expr') instanceof NameExpression)
            ) {
                continue;
            }
            throw new SyntaxError(
                'The text to be translated with "trans" can only contain references to simple variables',
                $lineNo
            );
        }
    }
}
