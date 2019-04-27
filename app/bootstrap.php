<?php

# create environment
$dotEnv = SkeletonCore\App::createEnvironment();
$dotEnv->overload();
$dotEnv->required("APP_NAME")->notEmpty();
$dotEnv->required("APP_ENV")->allowedValues(["development", "production"]);
$dotEnv->required("DB_HOSTNAME");
$dotEnv->required("DB_PORT")->isInteger();
$dotEnv->required("DB_USERNAME");
$dotEnv->required("DB_PASSWORD");
$dotEnv->required("DB_NAME");

# application instance
$app = new SkeletonCore\App;
$app->loadDatabaseConnection();

# load libraries

# load middlewares

# routes
require app_path("routes.php");

# run the application
$app->run();
