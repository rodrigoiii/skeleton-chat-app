<?php

/**
 * Assign in second parameter($file_list) all possible directory and file path.
 *
 * @param  string $path
 * @return void
 */
function get_paths($path, &$file_list)
{
    $files = glob("{$path}/*");

    while ($file = current($files))
    {
        array_push($file_list, $file);

        if (is_dir($file) && !dir_is_empty($file))
        {
            get_paths($file, $file_list);
        }

        next($files);
    }
}

/**
 * Return all possible file path.
 *
 * @param  string $path
 * @return array
 */
function get_files($path)
{
    $files = [];
    $paths = [];

    get_paths($path, $paths);

    while ($file = current($paths)) {
        if (is_file($file))
        {
            array_push($files, $file);
        }

        next($paths);
    }

    return $files;
}

/**
 * Check if a directory is empty (a directory with just '.svn' or '.git' is empty).
 * Source: https://gist.github.com/mistic100/8356949
 *
 * @param  string $dirname
 * @return bool
 */
function dir_is_empty($dirname)
{
    if (!is_dir($dirname)) return false;
    foreach (scandir($dirname) as $file)
    {
        if (!in_array($file, array('.','..','.svn','.git'))) return false;
    }
    return true;
}
