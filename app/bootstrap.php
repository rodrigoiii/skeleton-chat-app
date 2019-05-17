<?php
session_start();

# change default timezone
date_default_timezone_set(config('app.default_timezone'));

# create environment
$dotEnv = Core\App::createEnvironment();
$dotEnv->overload();
$dotEnv->required("APP_NAME")->notEmpty();
$dotEnv->required("APP_ENV")->allowedValues(["development", "production"]);
$dotEnv->required("DB_HOSTNAME");
$dotEnv->required("DB_PORT")->isInteger();
$dotEnv->required("DB_USERNAME");
$dotEnv->required("DB_PASSWORD");
$dotEnv->required("DB_NAME");

$dotEnv->required("USE_DIST")->isBoolean();
$dotEnv->required("APP_STATUS_UP")->isBoolean();
$dotEnv->required("DEBUG_BAR_ON")->isBoolean();

# application instance
$app = new Core\App;
$app->loadDatabaseConnection();

$container = $app->getContainer();

# load libraries
require core_path("libraries/debugbar.php");
require core_path("libraries/validator.php");

# load middlewares
$app->add("Core\\GlobalCsrfMiddleware");
$app->add($container->get('csrf'));
$app->add("Core\\OldInputMiddleware");
$app->add("Core\\AppStatusUpMiddleware");
$app->add("Core\\RemoveTrailingSlashMiddleware");

if (is_dev() && filter_var(env('DEBUG_BAR_ON'), FILTER_VALIDATE_BOOLEAN) && PHP_SAPI !== "cli")
{
    $app->add(new RunTracy\Middlewares\TracyMiddleware($app));
}

# routes
require app_path("routes.php");

# run the application
$app->run();
