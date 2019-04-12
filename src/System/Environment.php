<?php

namespace abhishek6262\NodePHP\System;

/**
 * Class Environment
 * @package abhishek6262\NodePHP\System
 */
class Environment
{
    /**
     * @var string
     */
    public $rootPath;

    /**
     * @var string
     */
    protected $nvmPath;

    /**
     * Environment constructor.
     *
     * @param  string $rootPath (Absolute Path [__DIR__]) The root directory of the project where package.json is located.
     * @param  string $binPath (Absolute Path [__DIR__]) The directory where the node js should be installed.
     */
    public function __construct(string $rootPath, string $binPath = '')
    {
        $this->rootPath = rtrim($rootPath, '/');
        $this->nvmPath = rtrim($binPath, '/');

        if (empty($this->nvmPath)) {
            $this->nvmPath = $this->rootPath . '/bin/nvm';
        }

        if (!file_exists($this->nvmPath)) {
            mkdir($this->nvmPath, 0777, true);
        }

        $this->setNodeEnvironment();
    }

    /**
     * Sets Node JS in the environment so its commands could be used.
     *
     * @return void
     */
    protected function setNodeEnvironment()
    {
        if ($this->nodeEnvironmentExists()) {
            $this->unsetNodeEnvironment();
        }

        $PATH = getenv('PATH');
        $PATH .= ':' . $this->getNodeBin();

        putenv('PATH=' . $PATH);
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
     * Unset Node JS from the environment making its commands no more
     * usable in the system.
     *
     * @return void
     */
    protected function unsetNodeEnvironment()
    {
        $env = ':' . $this->getNodeEnvironment();

        $PATH = str_replace($env, '', getenv('PATH'));
        putenv('PATH=' . $PATH);
    }

    /**
     * Returns the binary directory path of the recent version installed
     * of Node JS.
     *
     * @return string|null
     */
    public function getNodeBin(): ?string
    {
        $node = $this->getNodeVersion();

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
    public function getNodeVersion(): ?string
    {
        $path = $this->getNodePath() . '/*';
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
     * Returns the path where all the Node JS versions are installed.
     *
     * @return string
     */
    public function getNodePath(): string
    {
        return $this->nvmPath . '/versions/node';
    }

    /**
     * Install Node JS in the project.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function install()
    {
        $os = new \Tivie\OS\Detector();

        // NVM technique to install node js only works for unix since
        // it's unable to locate '~/.bash_profile' on windows. So it's
        // better to early terminate the installation process on it.
        if ($os->isWindowsLike()) {
            throw new \Exception('Node JS must be installed manually in Windows.');
        }

        $MAX_EXECUTION_TIME = 1800; // "30 Mins" for slow internet connections.

        set_time_limit($MAX_EXECUTION_TIME);

        if (!file_exists('~/.bash_profile')) {
            shell_exec('touch ~/.bash_profile');
        }

        // To specify where NVM should be installed in the project.
        putenv('NVM_DIR=' . $this->nvmPath);
        shell_exec('curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" && nvm install node');

        $this->setNodeEnvironment();
    }
}