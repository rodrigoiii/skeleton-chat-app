<?php

namespace Core\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @source: https://github.com/laravel/framework/blob/5.6/src/Illuminate/Console/Parser.php
 */
class CommandParser
{
    public static function parse($expression)
    {
        $name = static::name($expression);

        if (preg_match_all('/\{\s*(.*?)\s*\}/', $expression, $matches)) {
            if (count($matches[1])) {
                return array_merge([$name], static::parameters($matches[1]));
            }
        }

        return [$name, [], []];
    }

    private static function name($expression)
    {
        preg_match('/[^\s]+/', $expression, $matches);
        return $matches[0];
    }

    private static function parameters(array $tokens)
    {
        $arguments = [];

        $options = [];

        foreach ($tokens as $token) {
            if (preg_match('/-{2,}(.*)/', $token, $matches)) {
                $options[] = static::parseOption($matches[1]);
            } else {
                $arguments[] = static::parseArgument($token);
            }
        }

        return [$arguments, $options];
    }

    private static function parseArgument($token)
    {
        list($token, $description) = static::extractDescription($token);

        switch (true) {
            case preg_match("/\?\*$/", $token):
                return new InputArgument(trim($token, '?*'), InputArgument::IS_ARRAY, $description);
            case preg_match("/\*$/", $token):
                return new InputArgument(trim($token, '*'), InputArgument::IS_ARRAY | InputArgument::REQUIRED, $description);
            case preg_match("/\?$/", $token):
                return new InputArgument(trim($token, '?'), InputArgument::OPTIONAL, $description);
            case preg_match('/(.+)\=(.+)/', $token, $matches):
                return new InputArgument($matches[1], InputArgument::OPTIONAL, $description, $matches[2]);
            default:
                return new InputArgument($token, InputArgument::REQUIRED, $description);
        }
    }

    private static function parseOption($token)
    {
        list($token, $description) = static::extractDescription($token);

        $matches = preg_split('/\s*\|\s*/', $token, 2);

        if (isset($matches[1])) {
            $shortcut = $matches[0];
            $token = $matches[1];
        } else {
            $shortcut = null;
        }

        switch (true) {
            case preg_match("/\=$/", $token):
                return new InputOption(trim($token, '='), $shortcut, InputOption::VALUE_OPTIONAL, $description);
            case preg_match("/\=\*$/", $token):
                return new InputOption(trim($token, '=*'), $shortcut, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, $description);
            case preg_match('/(.+)\=(.+)/', $token, $matches):
                return new InputOption($matches[1], $shortcut, InputOption::VALUE_OPTIONAL, $description, $matches[2]);
            default:
                return new InputOption($token, $shortcut, InputOption::VALUE_NONE, $description);
        }
    }

    private static function extractDescription($token)
    {
        $parts = preg_split('/\s+:\s+/', trim($token), 2);

        return count($parts) === 2 ? $parts : [$token, null];
    }
}
