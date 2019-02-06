<?php

namespace abhishek6262\NodePHP;

/**
 * Trait NodeEnvironment
 * @package abhishek6262\NodePHP
 */
trait NodeEnvironment
{
    /**
     * Returns the binary directory path of the recent version installed
     * of Node JS.
     *
     * @return string|null
     */
    protected function getLatestNodeBin(): ?string
    {
        $node = $this->getLatestNodeVersion();

        if (empty($node)) {
            return null;
        }

        return $node . '/bin';
    }

    /**
     * Returns the recent version installed of Node JS.
     *
     * @return string|null
     */
    protected function getLatestNodeVersion(): ?string
    {
        $path  = $this->getNodePath() . '/*';
        $nodes = array_filter(glob($path), 'is_dir');

        if (empty($nodes)) {
            return null;
        }

        // Sort Node JS according to its latest version being the first.
        usort($nodes, function ($node1, $node2) {
            $node1 = ltrim($node1, 'v');
            $node2 = ltrim($node2, 'v');

            if (version_compare($node1, $node2, '==')) {
                return 0;
            }

            return version_compare($node1, $node2, '>') ? -1 : 1;
        });

        return $nodes[0];
    }

    /**
     * Returns the Node JS version with its path that's being used in the
     * environment.
     *
     * @return string|null
     */
    protected function getNodeEnvironment(): ?string
    {
        $variables = explode(':', getenv('PATH'));

        foreach ($variables as $variable) {
            if (strpos($variable, '/node/') !== false) {
                return trim($variable);
            }
        }

        return null;
    }

    /**
     * Returns the path where all the Node JS versions are installed.
     *
     * @return string
     */
    protected function getNodePath(): string
    {
        return $this->nvmPath . '/versions/node';
    }

    /**
     * Determines whether or not the Node JS has been set in the
     * environment.
     *
     * @return bool
     */
    protected function nodeEnvironmentExists(): bool
    {
        return $this->getNodeEnvironment() !== null ? true : false;
    }

    /**
     * Sets Node JS in the environment so its commands could be used.
     *
     * @return void
     */
    protected function setNodeEnvironment(): void
    {
        if ($this->nodeEnvironmentExists()) {
            $this->unsetNodeEnvironment();
        }

        $PATH = getenv('PATH');
        $PATH .= ':' . $this->getLatestNodeBin();

        putenv('PATH=' . $PATH);
    }

    /**
     * Unset Node JS from the environment making its commands no more
     * usable in the system.
     *
     * @return void
     */
    protected function unsetNodeEnvironment(): void
    {
        $env = ':' . $this->getNodeEnvironment();

        $PATH = str_replace($env, '', getenv('PATH'));
        putenv('PATH=' . $PATH);
    }
}