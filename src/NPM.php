<?php

namespace abhishek6262\NodePHP;

use abhishek6262\NodePHP\System\Environment;
use abhishek6262\NodePHP\System\Response;

/**
 * Class NPM
 * @package abhishek6262\NodePHP
 */
class NPM
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * NPM constructor.
     * 
     * @param  Environment $environment
     * @param  string $rootPath
     */
    public function __construct(Environment $environment, string $rootPath = '')
    {
        $this->environment = $environment;
        $this->rootPath    = $rootPath;

        if (empty($this->rootPath)) {
            $this->rootPath = $this->environment->rootPath;
        }
    }

    /**
     * Determines whether NPM has been installed in the project.
     *
     * @return bool
     */
    public function exists(): bool
    {
        $bin = $this->environment->getNodeBin();

        if (empty($bin)) {
            return false;
        } elseif (! file_exists($bin . '/npm')) {
            return false;
        }

        return true;
    }

    /**
     * Installs node environment in the project.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function install(): void
    {
        $this->environment->install();
    }

    /**
     * Installs the packages present in the package.json file.
     *
     * @return Response
     */
    public function installPackages(): Response
    {
        return $this->rawCommand('install');
    }

    /**
     * Runs the raw npm command.
     * 
     * @param  string $command
     * 
     * @return Response
     */
    public function rawCommand(string $command): Response
    {
        $CURRENT_WORKING_DIRECTORY = getcwd();

        chdir($this->rootPath);

        $MAX_EXECUTION_TIME = 1800; // "30 Mins" for slow internet connections.

        set_time_limit($MAX_EXECUTION_TIME);

        exec( escapeshellcmd('npm ' . $command . ' 2>&1'), $message, $code );

        chdir($CURRENT_WORKING_DIRECTORY);

        return new Response($message, $code);
    }

    /**
     * Determines whether the project has packages to be installed.
     *
     * @return bool
     */
    public function packagesExists(): bool
    {
        return file_exists($this->rootPath . '/package.json') ? true : false;
    }

    /**
     * Determines whether the packages are already installed in the
     * project.
     *
     * @return bool
     */
    public function packagesInstalled(): bool
    {
        return file_exists($this->rootPath . '/node_modules/') ? true : false;
    }
}