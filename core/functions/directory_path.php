<?php

/**
 * Return the root path of application.
 *
 * @param  string $str
 * @return string
 */
function base_path($str = "")
{
    $root = $_SERVER[PHP_SAPI !== "cli" ? "DOCUMENT_ROOT" : "PWD"];
    $is_own_server = count(glob("{$root}/.env")) === 0;
    $root .= $is_own_server ? "/.." : "";

    $str = !empty($str) ? ("/" . trim($str)) : "";

    return $root . $str;
}

/**
 * Return the app path of application.
 *
 * @param  string $str
 * @return string
 */
function app_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("app") . $str;
}

/**
 * Return the config path of application.
 *
 * @param  string $str
 * @return string
 */
function config_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("config") . $str;
}

/**
 * Return the core path of application.
 *
 * @param  string $str
 * @return string
 */
function core_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("core") . $str;
}

/**
 * Return the db path of application.
 *
 * @param  string $str
 * @return string
 */
function db_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("db") . $str;
}

/**
 * Return the public path of application.
 *
 * @param  string $str
 * @return string
 */
function public_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("public") . $str;
}

/**
 * Return the resources path of application.
 *
 * @param  string $str
 * @return string
 */
function resources_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("resources") . $str;
}

/**
 * Return the storage path of application.
 *
 * @param  string $str
 * @return string
 */
function storage_path($str = "")
{
    $str = !empty($str) ? ("/" . trim($str)) : "";
    return base_path("storage") . $str;
}
