<?php

/**
 * Convert string to title format.
 *
 * @param  string $str
 * @param  string $char
 * @return string
 */
function str_title($str, $char = "_")
{
    $title_array = array_map("ucfirst", explode($char, $str));
    return implode(" ", $title_array);
}
