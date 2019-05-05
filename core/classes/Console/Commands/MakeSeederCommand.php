<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeSeederCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:seeder {seeder}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create seeder template.";

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
        $seeder = $input->getArgument('seeder');

        try {
            if (!preg_match("/^[A-Z]\w*$/", $seeder)) throw new \Exception("Error: Invalid seeder name. It must be Characters and PascalCase.", 1);
            if (file_exists(db_path("seeds/{$seeder}.php"))) throw new \Exception("Error: The seeder name is already created.", 1);

            $is_created = $this->makeTemplate($seeder);

            $output->writeln($is_created ? "Successfully created." : "File not created. Check the file path.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Create the seeder template.
     *
     * @depends handle
     * @param  string $seeder
     * @return boolean
     */
    private function makeTemplate($seeder)
    {
        $file = __DIR__ . "/../templates/seeder.php.dist";
        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{seeder}}' => $seeder
            ]);

            $file_path = db_path("seeds/{$seeder}.php");

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }
}
