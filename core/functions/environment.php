<?php

/**
 * Enhance the getenv function.
 *
 * @param  string $key
 * @param  string $value
 * @return string
 */
if (!function_exists("env")) {
    function env($key, $value = "")
    {
        if (!empty($env = getenv($key)))
        {
            switch ($env) {
                case 'true':
                    return true;

                case 'false':
                    return false;

                case 'null':
                    return null;

                default:
                    return $env;
            }
        }

        return $value;
    }
}

/**
 * Return true if application environment is development.
 *
 * @return boolean
 */
function is_dev()
{
    return env('APP_ENV') == "development";
}

/**
 * Return true if application environment is production.
 *
 * @return boolean
 */
function is_prod()
{
    return env('APP_ENV') == "production";
}
