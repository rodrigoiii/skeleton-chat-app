<?php

namespace Core\Utilities;

class Session
{
    /**
     * Push key value data in $_SESSION variable.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  boolean $is_array
     * @return void
     */
    public static function set($key, $value, $is_array = false)
    {
        if ($is_array)
        {
            $_SESSION[$key][] = $value;
        }
        else
        {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Get the data in session using the key.
     * The $flash flag does either flash the session data or not.
     *
     * @param  string  $key
     * @param  boolean $flash
     * @return mixed
     */
    public static function get($key, $flash = false)
    {
        if (isset($_SESSION[$key]))
        {
            $session_value = $_SESSION[$key];
            if ($flash)
            {
                unset($_SESSION[$key]);
            }
            return $session_value;
        }

        return null;
    }

    /**
     * Check if the provided key is in session.
     *
     * @param  type  $key
     * @return boolean
     */
    public static function isExist($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Check if the provided key is empty in session.
     *
     * @param  type  $key
     * @return boolean
     */
    public static function isEmpty($key)
    {
        return empty($_SESSION[$key]);
    }

    /**
     * Return all data in session.
     *
     * @return array
     */
    public static function all()
    {
        return $_SESSION;
    }

    /**
     * Return all data except the provided keys.
     *
     * @param  array  $keys
     * @return array
     */
    public static function allExcept(array $keys)
    {
        $sessions = static::all();

        if (!empty($keys))
        {
            foreach ($keys as $key) {
                unset($sessions[$key]);
            }
        }

        return $sessions;
    }

    /**
     * Delete session via key.
     *
     * @param  array  $keys
     * @return void
     */
    public static function delete($key)
    {
        if (static::isExist($key))
        {
            unset($_SESSION[$key]);
        }
    }

    /**
     * If the argument $keys is empty, the function would be
     * destroy all stored in session. If not, remove only value
     * on the $keys variable.
     *
     * @param  array  $keys
     * @return void
     */
    public static function destroy(array $keys = [])
    {
        if (empty($keys))
        {
            session_destroy();
        }
        else
        {
            foreach ($keys as $key) {
                if (static::isExist($key))
                {
                    unset($_SESSION[$key]);
                }
            }
        }
    }
}
