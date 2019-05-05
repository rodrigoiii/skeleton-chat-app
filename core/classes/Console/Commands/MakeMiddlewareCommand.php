<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeMiddlewareCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:middleware {middleware}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create middleware class template.";

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
        $middleware = $input->getArgument('middleware');

        try {
            if (!preg_match("/^([A-Z]\w*\/?)*$/", $middleware)) throw new \Exception("Error: Invalid middleware name. It must be Characters and PascalCase.", 1);

            $pre_middleware_path = app_path("src/Middlewares");
            $sub_directories = "";

            // have directory
            if (strpos($middleware, "/"))
            {
                $explode_middleware = explode("/", $middleware);
                $middleware = array_pop($explode_middleware);

                $pre_middleware_path .= "/" . implode("/", $explode_middleware);
                $sub_directories .= "\\" . implode("\\", $explode_middleware);
            }

            if (file_exists("{$pre_middleware_path}/{$middleware}.php")) throw new \Exception("Error: The middleware name is already created.", 1);

            // create directory
            if (!file_exists($pre_middleware_path))
            {
                mkdir($pre_middleware_path, 0755, true);
            }

            $output->writeln($this->makeTemplate($sub_directories, $pre_middleware_path, $middleware) ? "Successfully created." : "File not created. Check the file path.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Create the middleware template.
     *
     * @depends handle
     * @param  string $sub_directories
     * @param  string $pre_middleware_path
     * @param  string $middleware
     * @return boolean
     */
    private function makeTemplate($sub_directories, $pre_middleware_path, $middleware)
    {
        $file = __DIR__ . "/../templates/middleware.php.dist";
        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{namespace}}' => get_app_namespace(),
                '{{sub_directories}}' => $sub_directories,
                '{{middleware}}' => $middleware
            ]);

            if (!file_exists(app_path("src/Middlewares")))
            {
                mkdir(app_path("src/Middlewares"), 0755, true);
            }

            $file_path = "{$pre_middleware_path}/{$middleware}.php";

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }
}
