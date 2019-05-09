<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeRequestCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:request {request}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create request class template.";

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
        $request = $input->getArgument('request');

        try {
            if (!preg_match("/^[A-Z]\w*$/", $request)) throw new \Exception("Error: Invalid request name. It must be Characters and PascalCase.", 1);
            if (file_exists(app_path("src/Requests/{$request}.php"))) throw new \Exception("Error: The request name is already created.", 1);

            $is_created = $this->makeTemplate($request);

            $output->writeln($is_created ? "Successfully created." : "File not created. Check the file path.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Create the request template.
     *
     * @depends handle
     * @param  string $request
     * @return boolean
     */
    private function makeTemplate($request)
    {
        $file = __DIR__ . "/../templates/request.php.dist";
        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{namespace}}' => get_app_namespace(),
                '{{request}}' => $request
            ]);

            if (!file_exists(app_path("src/Requests")))
            {
                mkdir(app_path("src/Requests"), 0755, true);
            }

            $file_path = app_path("src/Requests/{$request}.php");

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }
}
