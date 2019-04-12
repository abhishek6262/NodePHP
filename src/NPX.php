<?php

namespace abhishek6262\NodePHP;

use abhishek6262\NodePHP\System\Environment;
use abhishek6262\NodePHP\System\Response;

/**
 * Class NPX
 * @package abhishek6262\NodePHP
 */
class NPX
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
     * NPX constructor.
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
     * Determines whether NPX has been installed in the project.
     *
     * @return bool
     */
    public function exists(): bool
    {
        $bin = $this->environment->getNodeBin();

        if (empty($bin)) {
            return false;
        } elseif (! file_exists($bin . '/npx')) {
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
    public function install()
    {
        $this->environment->install();
    }

    /**
     * Runs the raw npx command.
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

        exec( escapeshellcmd('npx ' . $command . ' 2>&1'), $message, $code );

        chdir($CURRENT_WORKING_DIRECTORY);

        return new Response($message, $code);
    }
}