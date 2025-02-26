<?php

namespace jblond\TwigTrans;

use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Extract
 * @package jblond\TwigTrans
 */
final class Extract
{
    /**
     * @var Environment
     */
    protected Environment $environment;

    /**
     * Gettext parameters.
     *
     * @var string[]
     */
    protected array $parameters;

    /**
     * @var string
     */
    private string $executable = '';

    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->reset();
    }

    /**
     * @param string $executable
     * @return void
     * @psalm-suppress UnusedMethod
     */
    public function setExecutable(string $executable): void
    {
        $this->executable = $executable;
    }

    /**
     * @return void
     * @psalm-suppress UnusedMethod
     */
    protected function reset(): void
    {
        $this->parameters = [];
    }

    /**
     * @param string $path
     * @throws SyntaxError
     * @throws LoaderError
     * @throws RuntimeError
     * @return void
     */
    public function addTemplate(string $path): void
    {
        $this->environment->load($path);
    }

    /**
     * @param $parameter
     * @return void
     * @psalm-suppress UnusedMethod
     */
    public function addGettextParameter($parameter): void
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @param array $parameters
     * @return void
     * @psalm-suppress UnusedMethod
     */
    public function setGettextParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return void
     */
    public function extract(): void
    {
        $cacheDirectory = $this->environment->getCache();
        if ($cacheDirectory === false) {
            throw new RuntimeException('No cache directory is set');
        }
        $command = $this->executable ? : 'xgettext';
        $command .= ' ' . implode(' ', $this->parameters);
        $command .= ' ' . $cacheDirectory . '/*/*.php';
        echo $command;
        $error = 0;
        $output = system($command, $error);
        if (0 !== $error) {
            throw new RuntimeException(sprintf(
                'Gettext command "%s" failed with error code %s and output: %s',
                $command,
                $error,
                $output
            ));
        }

        $this->reset();
    }
}
