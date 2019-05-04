<?php

namespace Core\Console\Commands;

use Core\BaseCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Symfony\Component\Yaml\Yaml;

class TestCommand extends BaseCommand
{
    const ENVIRONMENT = "testing";

    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "test {--ignore-env : Ignore to change the APP_ENV in .env file to testing} {--ignore-yml : Ignore to change the default database in phinx.yml file to testing}";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Change application environment to testing.";

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
     * @return boolean|string
     */
    public function handle(Input $input, Output $output)
    {
        $ignore_env = !is_null($input->getOption('ignore-env')) ? $input->getOption('ignore-env') : false;
        $ignore_yml = !is_null($input->getOption('ignore-yml')) ? $input->getOption('ignore-yml') : false;

        try {
            if (!$this->isEnvFileExist()) throw new \Exception(".env file is not exist.", 1);
            if ($this->isAppEnvAlreadyTesting()) throw new \Exception("Application environment is already ".static::ENVIRONMENT.".", 1);

            $output->writeln(
                $this->changeAppEnv(static::ENVIRONMENT) ?
                "Application environment is now ".static::ENVIRONMENT."!" :
                "Cannot change application environment"
            );

            if (!$ignore_env || !$ignore_yml)
            {
                if (!$this->isPhinxYmlFileExist()) throw new \Exception("Error: phinx.yml file is not exist", 1);

                if (!$ignore_env)
                {
                    if ($this->isAppDbNameAlreadyForTesting()) throw new \Exception("Error: The application DB_NAME is already for ".static::ENVIRONMENT.".", 1);

                    $phinx_yml = $this->getParsePhinxYml();
                    $phinx_db_name = $phinx_yml['environments'][static::ENVIRONMENT]['name'];
                    $is_changed = $this->changeAppDbName($phinx_db_name);

                    $output->writeln(
                        $is_changed ?
                        "DB_NAME in .env is now {$phinx_db_name}!" :
                        "Cannot change DB_NAME in .env file"
                    );
                }

                if (!$ignore_yml)
                {
                    $phinx_yml = $this->getParsePhinxYml();

                    if (!isset($phinx_yml['environments'])) throw new \Exception("Error: No environments key inside of phinx.yml.", 1);
                    if (!isset($phinx_yml['environments']['default_database'])) throw new \Exception("Error: No environments.default_database key inside of phinx.yml.", 1);
                    if ($phinx_yml['environments']['default_database'] === static::ENVIRONMENT) throw new \Exception("Error: Default database is already ".static::ENVIRONMENT." inside of phinx.yml.", 1);

                    $is_changed = $this->changePhinxYmlDefaultDatabase(static::ENVIRONMENT);

                    $output->writeln(
                        $is_changed ?
                        "Default database in phinx.yml file is now ".static::ENVIRONMENT."!" :
                        "Cannot change default database in phinx.yml file"
                    );
                }
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function isEnvFileExist()
    {
        $env_path = base_path(".env");
        return file_exists($env_path);
    }

    public function isPhinxYmlFileExist()
    {
        $phinx_yml_path = base_path("phinx.yml");
        return file_exists($phinx_yml_path);
    }

    public function isAppEnvAlreadyTesting()
    {
        $old_env = config("app.env");
        return static::ENVIRONMENT === $old_env;
    }

    public function isAppDbNameAlreadyForTesting()
    {
        $phinx_yml = $this->getParsePhinxYml();

        if (!is_null($phinx_yml))
        {
            $db_config = config("database");

            try {
                if (!isset($phinx_yml['environments'])) throw new \Exception("Error: No environments key inside of phinx.yml.", 1);
                if (!isset($phinx_yml['environments']['default_database'])) throw new \Exception("Error: No environments.default_database key inside of phinx.yml.", 1);
                if ($phinx_yml['environments']['default_database'] === static::ENVIRONMENT) throw new \Exception("Error: Default database is already ".static::ENVIRONMENT." inside of phinx.yml.", 1);
                if (!isset($phinx_yml['environments'][static::ENVIRONMENT])) throw new \Exception("Error: No environments.".static::ENVIRONMENT." key inside of phinx.yml.", 1);
                if (!isset($phinx_yml['environments'][static::ENVIRONMENT]['name'])) throw new \Exception("Error: The environments.".static::ENVIRONMENT.".name must define in phinx.yml.", 1);
                if (empty($phinx_yml['environments'][static::ENVIRONMENT]['name'])) throw new \Exception("Error: The environments.".static::ENVIRONMENT.".name must not empty phinx.yml.", 1);

                return $phinx_yml['environments'][static::ENVIRONMENT]['name'] === $db_config['database'];
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }

        return null;
    }

    public function getParsePhinxYml()
    {
        if ($this->isPhinxYmlFileExist())
        {
            $phinx_yml_path = base_path("phinx.yml");
            return Yaml::parseFile($phinx_yml_path);
        }

        return null;
    }

    public function changeAppEnv($app_env)
    {
        if ($this->isEnvFileExist()) {
            $env_path = base_path(".env");
            $old_env = config("app.env");

            file_put_contents($env_path, str_replace("APP_ENV={$old_env}", "APP_ENV={$app_env}", file_get_contents($env_path)));

            return true;
        }

        return false;
    }

    public function changePhinxYmlDefaultDatabase($app_env)
    {
        $phinx_yml = $this->getParsePhinxYml();

        if (!is_null($phinx_yml))
        {
            $phinx_yml_path = base_path("phinx.yml");

            $phinx_yml['environments']['default_database'] = $app_env;
            file_put_contents($phinx_yml_path, Yaml::dump($phinx_yml, 3));

            return true;
        }

        return false;
    }

    public function changeAppDbName($db_name)
    {
        if ($this->isEnvFileExist()) {
            $env_path = base_path(".env");
            $db_config = config("database");

            file_put_contents($env_path, str_replace("DB_NAME=".$db_config['database'], "DB_NAME={$db_name}", file_get_contents($env_path)));

            return true;
        }

        return false;
    }

    /**
     * @depends handle
     * @return void
     */
    public function changeDbInPhinxYml(Output $output)
    {
        if ($this->isPhinxYmlFileExist())
        {
            try {
                $phinx_yml = Yaml::parseFile($phinx_yml_path);

                if (!isset($phinx_yml['environments'])) throw new \Exception("Error: No environments key inside of phinx.yml.", 1);
                if (!isset($phinx_yml['environments']['default_database'])) throw new \Exception("Error: No environments.default_database key inside of phinx.yml.", 1);
                if ($phinx_yml['environments']['default_database'] === static::ENVIRONMENT) throw new \Exception("Error: Default database is already ".static::ENVIRONMENT." inside of phinx.yml.", 1);

                $phinx_yml['environments']['default_database'] = static::ENVIRONMENT;
                file_put_contents($phinx_yml_path, Yaml::dump($phinx_yml, 3));
                $output->writeln("Default database in phinx.yml file is now ".static::ENVIRONMENT."!");
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }
        }
    }

    /**
     * @depends changeDbInPhinxYml
     * @param array $phinx_yml
     * @return void
     */
    public function changeDbInEnv(Output $output)
    {
        $env_path = base_path(".env");
        $phinx_yml_path = base_path("phinx.yml");

        $db_config = config("database");

        try {
            $phinx_yml = Yaml::parseFile($phinx_yml_path);

            if (!isset($phinx_yml['environments'])) throw new \Exception("Error: No environments key inside of phinx.yml.", 1);
            if (!isset($phinx_yml['environments'][static::ENVIRONMENT])) throw new \Exception("Error: No environments.".static::ENVIRONMENT." key inside of phinx.yml.", 1);
            if (!isset($phinx_yml['environments'][static::ENVIRONMENT]['name'])) throw new \Exception("Error: The environments.".static::ENVIRONMENT.".name must define in phinx.yml.", 1);
            if (empty($phinx_yml['environments'][static::ENVIRONMENT]['name'])) throw new \Exception("Error: The environments.".static::ENVIRONMENT.".name must not empty phinx.yml.", 1);
            if ($phinx_yml['environments'][static::ENVIRONMENT]['name'] === $db_config['database']) throw new \Exception("Error: The environments.".static::ENVIRONMENT.".name is already ".$db_config['database']." in phinx.yml.", 1);

            $phinx_db_name = $phinx_yml['environments'][static::ENVIRONMENT]['name'];

            file_put_contents($env_path, str_replace("DB_NAME=".$db_config['database'], "DB_NAME={$phinx_db_name}", file_get_contents($env_path)));
            $output->writeln("DB_NAME is now {$phinx_db_name}!");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
