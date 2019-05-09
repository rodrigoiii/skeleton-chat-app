<?php

use Slim\Flash\Messages;

/**
 * Short-cut of slim flash message.
 *
 * @param  boolean $is_passed
 * @param  array $pass_message
 * @param  array $fail_message
 * @return void
 */
function flash($is_passed, array $pass_message, array $fail_message)
{
    $flash = new Messages;
    if ($is_passed)
    {
        $key = key($pass_message);
        $flash->addMessage($key, $pass_message[$key]);
    }
    else
    {
        $key = key($fail_message);
        $flash->addMessage($key, $fail_message[$key]);
    }
}
