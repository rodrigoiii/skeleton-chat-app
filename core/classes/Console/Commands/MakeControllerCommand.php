<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeControllerCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:controller {controller} {--r|resource}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create controller class template.";

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
        $controller = $input->getArgument('controller');
        $is_resource = $input->getOption('resource');

        try {
            if (!preg_match("/^([A-Z]\w*\/?)*$/", $controller)) throw new \Exception("Error: Invalid controller name. It must be Characters and PascalCase.", 1);

            $this->setControllerParts($pre_controller_path, $sub_directories, $controller);

            $controller_path = "{$pre_controller_path}/{$controller}.php";
            if ($this->isControllerExist($controller_path)) throw new \Exception("Error: The controller name is already created.", 1);

            $this->createPreControllerIfNotExist($pre_controller_path);
            $is_created = $this->createController($sub_directories, $controller_path, $is_resource);

            $output->writeln($is_created ? "Successfully created." : "Cannot create controller");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function setControllerParts(&$pre_controller_path, &$sub_directories, &$controller)
    {
        $pre_controller_path = app_path("src/Controllers");
        $sub_directories = "";

        if (strpos($controller, "/") !== false)
        {
            $explode_controller = explode("/", $controller);
            $controller = array_pop($explode_controller);
            $pre_controller_path .= "/" . implode("/", $explode_controller);
            $sub_directories .= "\\" . implode("\\", $explode_controller);
        }
    }

    public function isControllerExist($controller_path)
    {
        return file_exists($controller_path);
    }

    public function createPreControllerIfNotExist($pre_controller_path)
    {
        if (!file_exists($pre_controller_path))
        {
            mkdir($pre_controller_path, 0755, true);
        }
    }

    public function createController($sub_directories, $controller_path, $is_resource = false)
    {
        $file = __DIR__ . "/../templates/controller/";
        $file .= $is_resource ? "controller-resource.php.dist" : "controller.php.dist";

        $pre_controller_path = substr($controller_path, 0, strrpos($controller_path, "/", 1));
        $controller = basename($controller_path, ".php");

        if (!file_exists($pre_controller_path))
        {
            mkdir($pre_controller_path, 0755, true);
        }

        $template = strtr(file_get_contents($file), [
            '{{namespace}}' => get_app_namespace(),
            '{{sub_directories}}' => $sub_directories,
            '{{controller}}' => $controller
        ]);

        $file_controller = fopen($controller_path, "w");
        fwrite($file_controller, $template);
        fclose($file_controller);

        return file_exists($controller_path);
    }
}
