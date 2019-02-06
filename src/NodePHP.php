<?php

namespace abhishek6262\NodePHP;

/**
 * Class NodePHP
 * @package abhishek6262\NodePHP
 */
class NodePHP
{
    use NodeEnvironment;

    /**
     * @var string
     */
    protected $nvmPath;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * NodePHP constructor.
     *
     * @param  string $projectRootPath (Absolute Path [__DIR__]) The root directory of the project where package.json file is stored.
     * @param  string $binDirPath (Absolute Path [__DIR__]) The directory where the node js should be installed.
     */
    public function __construct(string $projectRootPath, string $binDirPath = '')
    {
        $this->rootPath = rtrim($projectRootPath, '/');
        $this->nvmPath  = rtrim($binDirPath, '/');

        if (empty($this->nvmPath)) {
            $this->nvmPath = $this->rootPath . '/bin/nvm';
        }

        if (! file_exists($this->nvmPath)) {
            mkdir($this->nvmPath, 0777, true);
        }

        $this->setNodeEnvironment();
    }

    /**
     * Determines whether Node JS has been installed in the project.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return empty($this->getLatestNodeBin()) === false;
    }

    /**
     * Install Node JS in the project.
     *
     * @return void
     * 
     * @throws \Exception
     */
    public function install(): void
    {
        $os = new \Tivie\OS\Detector();

        // NVM technique to install node js only works for unix since
        // it's unable to locate '~/.bash_profile' on windows. So it's
        // better to early terminate the installation process on it.
        if ($os->isWindowsLike()) {
            throw new \Exception('Node JS must be installed manually in Windows.');
        }

        $CURRENT_WORKING_DIRECTORY = getcwd();

        chdir($this->rootPath);

        $MAX_EXECUTION_TIME = 1800; // "30 Mins" for slow internet connections.

        set_time_limit($MAX_EXECUTION_TIME);

        if (! file_exists('~/.bash_profile')) {
            shell_exec('touch ~/.bash_profile');
        }

        // To specify where NVM should be installed in the project.
        putenv('NVM_DIR=' . $this->nvmPath);
        shell_exec('curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" && nvm install node');

        $this->setNodeEnvironment();

        chdir($CURRENT_WORKING_DIRECTORY);
    }

    /**
     * Executes the raw node command.
     *
     * @param  string $command
     *
     * @return void
     */
    public function rawNodeCommand(string $command): void
    {
        $CURRENT_WORKING_DIRECTORY = getcwd();

        chdir($this->rootPath);

        $MAX_EXECUTION_TIME = 1800; // "30 Mins" for slow internet connections.

        set_time_limit($MAX_EXECUTION_TIME);

        $escaped_command = escapeshellcmd('node ' . $command);
        shell_exec($escaped_command);

        chdir($CURRENT_WORKING_DIRECTORY);
    }

    /**
     * Executes the raw npm command.
     *
     * @param  string $command
     *
     * @return void
     */
    public function rawNpmCommand(string $command): void
    {
        $CURRENT_WORKING_DIRECTORY = getcwd();

        chdir($this->rootPath);

        $MAX_EXECUTION_TIME = 1800; // "30 Mins" for slow internet connections.

        set_time_limit($MAX_EXECUTION_TIME);

        $escaped_command = escapeshellcmd('npm ' . $command);
        shell_exec($escaped_command);

        chdir($CURRENT_WORKING_DIRECTORY);
    }

    /**
     * Executes the raw npx command.
     *
     * @param  string $command
     *
     * @return void
     */
    public function rawNpxCommand(string $command): void
    {
        $CURRENT_WORKING_DIRECTORY = getcwd();

        chdir($this->rootPath);

        $MAX_EXECUTION_TIME = 1800; // "30 Mins" for slow internet connections.

        set_time_limit($MAX_EXECUTION_TIME);

        $escaped_command = escapeshellcmd('npx ' . $command);
        shell_exec($escaped_command);

        chdir($CURRENT_WORKING_DIRECTORY);
    }

    /**
     * Installs the packages present in the package.json file.
     *
     * @return void
     */
    public function installNpmPackages(): void
    {
        $this->rawNpmCommand('install');
    }

    /**
     * Determines whether the project has packages to be installed.
     *
     * @return bool
     */
    public function npmPackagesExists(): bool
    {
        return file_exists($this->rootPath . '/package.json') ? true : false;
    }

    /**
     * Determines whether the packages are already installed in the
     * project.
     *
     * @return bool
     */
    public function npmPackagesInstalled(): bool
    {
        return file_exists($this->rootPath . '/node_modules/') ? true : false;
    }
}