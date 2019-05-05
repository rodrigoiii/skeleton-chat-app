<?php

namespace Core;

use Core\Console\CommandParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class BaseCommand extends Command
{
    /**
     * Console command signature
     *
     * @var string
     */
    private $signature;

    /**
     * Console command description
     *
     * @var string
     */
    private $description;

    /**
     * The name of command to execute it
     *
     * @var string
     */
    private $name;

    /**
     * Arguments came from user input
     *
     * @var string
     */
    private $arguments;

    /**
     * Options came from user input
     *
     * @var string
     */
    private $optional;

    /**
     * Create a new command instance
     *
     * @param string $signature
     * @param string $description
     */
    public function __construct($signature, $description)
    {
        $this->signature = $signature;
        $this->description = $description;

        list($this->name, $this->arguments, $this->options) = CommandParser::parse($this->signature);

        parent::__construct();
    }

    /**
     * Set the name and description
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName($this->name);
        $this->setDescription($this->description);
        $this->getDefinition()->addArguments($this->arguments);
        $this->getDefinition()->addOptions($this->options);
    }

    /**
     * Execute the console command
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->handle($input, $output);
    }
}
