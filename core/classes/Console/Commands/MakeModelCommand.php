<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeModelCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:model {model}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create model class template.";

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
        $model = $input->getArgument('model');

        try {
            if (!preg_match("/^([A-Z]\w*\/?)*$/", $model)) throw new \Exception("Error: Invalid model name. It must be Characters and PascalCase.", 1);

            $pre_model_path = app_path("src/Models");
            $sub_directories = "";

            // have directory
            if (strpos($model, "/"))
            {
                $explode_model = explode("/", $model);
                $model = array_pop($explode_model);

                $pre_model_path .= "/" . implode("/", $explode_model);
                $sub_directories = "\\" . implode("\\", $explode_model);
            }

            if (file_exists("{$pre_model_path}/{$model}.php")) throw new \Exception("Error: The model name is already created.", 1);

            // create directory
            if (!file_exists($pre_model_path))
            {
                mkdir($pre_model_path, 0755, true);
            }

            $output->writeln($this->makeTemplate($sub_directories, $pre_model_path, $model) ? "Successfully created." : "File not created. Check the file path.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Create the model template
     *
     * @depends handle
     * @param  string $sub_directories
     * @param  string $pre_model_path
     * @param  string $model
     * @return boolean
     */
    private function makeTemplate($sub_directories, $pre_model_path, $model)
    {
        $file = __DIR__ . "/../templates/model.php.dist";

        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{namespace}}' => get_app_namespace(),
                '{{sub_directories}}' => $sub_directories,
                '{{model}}' => $model
            ]);

            $file_path = "{$pre_model_path}/{$model}.php";

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }
}
