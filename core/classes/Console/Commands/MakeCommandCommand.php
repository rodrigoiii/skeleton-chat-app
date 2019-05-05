<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeCommandCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:command {_command}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create command class template.";

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
     * @return boolean|string
     */
    public function handle(Input $input, Output $output)
    {
        $command = $input->getArgument('_command');

        try {
            if (!$this->checkCommand($command)) throw new \Exception("Error: Invalid command name. It must be Characters and PascalCase.", 1);
            if ($this->isCommandExist($command)) throw new \Exception("Error: The command name is already created.", 1);

            $output->writeln($this->makeTemplate($command) ?
                            "Successfully created." :
                            "Cannot create command template");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function checkCommand($command)
    {
        return preg_match("/^[A-Z]\w*$/", $command);
    }

    public function isCommandExist($command)
    {
        return file_exists(app_path("src/Commands/{$command}.php"));
    }

    /**
     * Create the command template.
     *
     * @depends handle
     * @param  string $command
     * @return boolean
     */
    private function makeTemplate($command)
    {
        $file = __DIR__ . "/../templates/command.php.dist";

        try {
            if (!file_exists($file)) throw new \Exception("{$file} file is not exist.", 1);

            $template = strtr(file_get_contents($file), [
                '{{namespace}}' => get_app_namespace(),
                '{{command}}' => $command,
                '{{command_name}}' => strtolower($command)
            ]);

            if (!file_exists(app_path("src/Commands")))
            {
                mkdir(app_path("src/Commands"), 0755, true);
            }

            $file_path = app_path("src/Commands/{$command}.php");

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);

        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }

        return false;
    }
}
