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

/**
 * Upload file in public/uploads folder.
 *
 * @param  Slim\Http\UploadedFile $file
 * @param  string $destination
 * @param  boolean $hast_it
 * @return string
 */
function upload($file, $destination="", $hash_it=true)
{
    $upload_path = public_path("uploads");

    if ($file->getError() === UPLOAD_ERR_OK)
    {
        $destination = trim($destination, "/");

        if (!empty($destination))
        {
            $directories = explode("/", $destination);
            $directories_num = count($directories);

            for ($i=0; $i < $directories_num; $i++) {
                $directory_to_be_created = "";
                for ($j=0; $j <= $i; $j++) {
                    $directory_to_be_created .= $directories[$j] . "/";
                }

                if (!file_exists("{$upload_path}/{$directory_to_be_created}"))
                {
                    mkdir("{$upload_path}/{$directory_to_be_created}", 0755, true);
                }
            }

            $destination = "/{$destination}";
        }

        $type = explode("/", $file->getClientMediaType());
        $extension = $type[1];
        $filename = basename($file->getClientFilename(), ".{$extension}");
        $filename = ($hash_it ? uniqid() : $filename) . "." . $extension;

        $file_path = "/{$upload_path}{$destination}/{$filename}";
        $file->moveTo($file_path);

        $web_file_path = str_replace(public_path() . "/", "", $file_path);

        return $web_file_path;
    }

    return null;
}
