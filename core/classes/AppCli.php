<?php

namespace Core;

use Dotenv\Dotenv;
use Dotenv\Environment\Adapter\PutenvAdapter;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Database\Capsule\Manager;
use Respect\Validation\Validator;
use Symfony\Component\Console\Application;

class AppCli
{
    /**
     * @var Application
     */
    protected $cli_app;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Initialize the cli application.
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->cli_app = new Application($settings['name']);

        $this->boot();
    }

    /**
     * Boot the cli application.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadDatabaseConnection();

        $commands = [];
        $this->pushCommands($commands);
        $this->pushFoundationCommands($commands);

        $this->cli_app->addCommands($commands);
    }

    /**
     * Run the cli application.
     *
     * @return void
     */
    public function run()
    {
        $this->cli_app->run();
    }

    /**
     * Create environment
     *
     * @return void
     */
    public static function createEnvironment()
    {
        try {
            $dotEnvFactory = new DotenvFactory([
                new PutenvAdapter
            ]);

            return Dotenv::create(base_path(), null, $dotEnvFactory);
        } catch (InvalidPathException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Load the database connection of the application.
     *
     * @param  Psr\Container\ContainerInterface $container
     * @return void
     */
    private function loadDatabaseConnection()
    {
        /**
         * Setup for 'illuminate/database'
         */
        $capsule = new Manager;
        $capsule->addConnection($this->settings['database']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $capsule::connection();
    }

    /**
     * Push all commands on app/Commands directory to $commands variable.
     * @param  array  &$commands
     * @return void
     */
    private function pushCommands(array &$commands)
    {
        $glob_path = glob(app_path('src/Commands/*.php'));

        $commands = array_map(function($absolute_path_file) {
            $base_file = basename($absolute_path_file, ".php");

            $command_class = get_app_namespace() . "Commands\\" . $base_file;

            return new $command_class;
        }, $glob_path);
    }

    /**
     * Push all commands on core/classes/Console/Commands directory to $commands variable.
     * @param  array  &$commands
     * @return void
     */
    private function pushFoundationCommands(array &$commands)
    {
        $glob_path = glob(core_path('classes/Console/Commands/*.php'));

        $commands = array_map(function($absolute_path_file) {
            $base_file = basename($absolute_path_file, ".php");

            $command_class = "Core\Console\Commands\\" . $base_file;

            return new $command_class;
        }, $glob_path);
    }
}
