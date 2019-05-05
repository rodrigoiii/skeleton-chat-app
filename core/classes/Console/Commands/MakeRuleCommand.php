<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class MakeRuleCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "make:rule {rule} {--c|choose-template : Add method chooseTemplate in exception class for complex error message.}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Create rule and exception class template.";

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
        $rule = $input->getArgument('rule');
        $w_choose_template = $input->getOption('choose-template');

        try {
            if (!preg_match("/^[A-Z]\w*$/", $rule)) throw new \Exception("Error: Invalid rule name. It must be Characters and PascalCase.", 1);
            if (file_exists(app_path("src/Validation/Rules/{$rule}.php"))) throw new \Exception("Error: The rule name is already created.", 1);

            if (!file_exists(app_path("src/Validation")))
            {
                mkdir(app_path("src/Validation"), 0755, true);
            }

            $output->writeln($this->ruleTemplate($rule) ? "Successfully created rule class." : "Rule file not created. Check the file path.");
            $output->writeln($this->exceptionTemplate($rule, $w_choose_template) ? "Successfully created exception class." : "Exception file not created. Check the file path.");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Create the rule template.
     *
     * @param  string $rule
     * @return boolean
     */
    private function ruleTemplate($rule)
    {
        $file = __DIR__ . "/../templates/validator/rule.php.dist";

        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{namespace}}' => get_app_namespace(),
                '{{rule}}' => $rule
            ]);

            if (!file_exists(app_path("src/Validation/Rules")))
            {
                mkdir(app_path("src/Validation/Rules"), 0755, true);
            }

            $file_path = app_path("src/Validation/Rules/{$rule}.php");

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }

    /**
     * Create the exception template.
     *
     * @param  string $rule
     * @param  string $w_choose_template
     * @return boolean
     */
    private function exceptionTemplate($rule, $w_choose_template)
    {
        $file = __DIR__ . "/../templates/validator/";
        $file .= $w_choose_template ? "exception-with-choose-template.php.dist" : "exception.php.dist";

        if (file_exists($file))
        {
            $template = strtr(file_get_contents($file), [
                '{{namespace}}' => get_app_namespace(),
                '{{rule}}' => $rule
            ]);

            if (!file_exists(app_path("src/Validation/Exceptions")))
            {
                mkdir(app_path("src/Validation/Exceptions"), 0755, true);
            }

            $file_path = app_path("src/Validation/Exceptions/{$rule}Exception.php");

            $file = fopen($file_path, "w");
            fwrite($file, $template);
            fclose($file);

            return file_exists($file_path);
        }

        return false;
    }
}
