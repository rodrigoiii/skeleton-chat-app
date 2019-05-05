<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class DownCommand extends BaseCommand
{
    const APP_MODE = "down";

    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "down";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Down the application.";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct($this->signature, $this->description);
    }

    /**
     * To be call after execute the command.
     *
     * @param  Input $input
     * @param  Output $output
     * @return void
     */
    public function handle(Input $input, Output $output)
    {
        try {
            if (!$this->isEnvFileExist()) throw new \Exception(".env file is not exist.", 1);
            if ($this->isAppModeAlreadyDown()) throw new \Exception("Application mode is already ".static::APP_MODE.".", 1);

            $output->writeln($this->downApplicationMode() ?
                "Application mode is now ".static::APP_MODE."!" :
                "Cannot ".static::APP_MODE." the application mode"
            );
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function isEnvFileExist()
    {
        $env_path = base_path(".env");
        return file_exists($env_path);
    }

    public function isAppModeAlreadyDown()
    {
        $old_mode = config("app.mode");
        return static::APP_MODE === $old_mode;
    }

    public function downApplicationMode()
    {
        $env_path = base_path(".env");
        $old_mode = config("app.mode");

        file_put_contents($env_path, str_replace("APP_MODE={$old_mode}", "APP_MODE=".static::APP_MODE."", file_get_contents($env_path)));
        return true;
    }
}
