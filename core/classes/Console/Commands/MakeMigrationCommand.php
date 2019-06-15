<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeMigrationCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:migration {migration} {--template= : It must be 'add-table', 'add-column' or 'change-column'.}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create migration template.";

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
        $migration = $input->getArgument('migration');
        $template = !is_null($input->getOption('template')) ? $input->getOption('template') : "add-table";

        try {
            if (!preg_match("/^[A-Z]\w*$/", $migration)) throw new \Exception("Error: Invalid migration name. It must be Characters and PascalCase.", 1);
            if (!in_array($template, ["add-table", "add-column", "change-column"])) throw new \Exception("Invalid template option. It must be 'add-table', 'add-column' or 'change-column'.", 1);

            $output->writeln($this->executeCommand("php phinx create {$migration} -t ./core/classes/Console/templates/migration/{$template}.php.dist"));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function executeCommand($command)
    {
        return shell_exec($command);
    }
}
