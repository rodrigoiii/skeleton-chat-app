<?php

namespace Core;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    const LOGS = ["debug", "info", "notice", "warning", "error", "alert", "emergency"];
    const LOGS_ENV_ANY = 0;
    const LOGS_ENV_DEV = 1;
    const LOGS_ENV_PROD = 2;

    /**
     * Ready the logger object.
     *
     * @return mixed
     */
    private static function logger()
    {
        $now = date("Y-m-d");
        $log_file = PHP_SAPI === "cli" ? "{$now}-cli.log" : "{$now}.log";

        $settings = [
            'name' => config('app.name'),
            'level' => Logger::DEBUG,
            'path' => storage_path("logs/{$log_file}")
        ];

        $logger = new Logger($settings['name']);
        $handler = new StreamHandler($settings['path']);

        $lineFormatter = new LineFormatter;
        $lineFormatter->includeStacktraces();

        $handler->setFormatter($lineFormatter);

        return $logger->pushHandler($handler, $settings['level']);
    }

    /**
     * Log available:
     * - debug
     * - info
     * - notice
     * - warning
     * - error
     * - alert
     * - emergency
     *
     * @param  string $name
     * @param  array $args
     * @return void
     */
    public static function __callStatic($name, $args)
    {
        $error_message = array_shift($args);
        $log_environment = array_shift($args);

        $log_environment = is_null($log_environment) ? 0 : $log_environment;

        if (in_array($name, static::LOGS))
        {
            if (!empty($error_message))
            {
                switch ($log_environment) {
                    case static::LOGS_ENV_ANY:
                        static::logger()->$name($error_message);

                        break;

                    case static::LOGS_ENV_DEV:
                        if (is_dev())
                        {
                            static::logger()->$name($error_message);
                        }
                        break;

                    case static::LOGS_ENV_PROD:
                        if (is_prod())
                        {
                            static::logger()->$name($error_message);
                        }
                        break;
                }
            }
            else
            {
                error_log("Error: Error message is empty!");
            }
        }
        else
        {
            error_log("Error: Invalid log method!");
        }
    }
}
