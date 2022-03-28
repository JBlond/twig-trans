<?php

namespace jblond\TwigTrans;

use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 *
 */
class Extract
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * Gettext parameters.
     *
     * @var string[]
     */
    protected $parameters;

    /**
     * @var
     */
    private $executable;

    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->reset();
    }

    /**
     * @param mixed $executable
     * @return void
     */
    public function setExecutable($executable): void
    {
        $this->executable = $executable;
    }

    /**
     * @return void
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
     */
    public function addGettextParameter($parameter): void
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @param array $parameters
     * @return void
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
