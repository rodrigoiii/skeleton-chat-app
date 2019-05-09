<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeDebugbarCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:debugbar {debugbar}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create debugbar class template.";

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
        $debugbar = $input->getArgument('debugbar');

        try {
            if (!preg_match("/^[A-Z]\w*$/", $debugbar)) throw new \Exception("Error: Invalid debugbar name. It must be Characters and PascalCase.", 1);
            if ($this->isDebugbarExist($debugbar)) throw new \Exception("Error: The debugbar name is already created.", 1);

            $is_created = $this->makeTemplate($debugbar);

            $output->writeln($is_created ? "Successfully created." : "File not created. Check the file path.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function isDebugbarExist($debugbar)
    {
        return file_exists(app_path("src/Debugbars/{$debugbar}.php"));
    }

    /**
     * Create the debugbar template.
     *
     * @depends handle
     * @param  string $debugbar
     * @return boolean
     */
    private function makeTemplate($debugbar)
    {
        $file = __DIR__ . "/../templates/debugbar.php.dist";

        $template = strtr(file_get_contents($file), [
            '{{namespace}}' => get_app_namespace(),
            '{{debugbar}}' => $debugbar
        ]);

        if (!file_exists(app_path("src/Debugbars")))
        {
            mkdir(app_path("src/Debugbars"), 0755, true);
        }

        $file_path = app_path("src/Debugbars/{$debugbar}.php");

        $file = fopen($file_path, "w");
        fwrite($file, $template);
        fclose($file);

        return file_exists($file_path);
    }
}
