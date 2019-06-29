<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class UpCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "up";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Up the application.";

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
            if ($this->isAppStatusAlreadyUp()) throw new \Exception("Application status is already up.", 1);

            $output->writeln($this->upApplicationStatus() ?
                "Application status is now up!" :
                "Cannot up the application status"
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

    public function isAppStatusAlreadyUp()
    {
        $old_status = config("app.status_up");
        return $old_status == true;
    }

    public function upApplicationStatus()
    {
        $env_path = base_path(".env");
        $old_status = config("app.status_up") ? "true" : "false";

        file_put_contents($env_path, str_replace("APP_STATUS_UP={$old_status}", "APP_STATUS_UP=true", file_get_contents($env_path)));
        return true;
    }
}
