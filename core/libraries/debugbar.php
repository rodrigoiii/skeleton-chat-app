<?php

use Tracy\Debugger;

if (is_dev() && filter_var(env('DEBUG_BAR_ON'), FILTER_VALIDATE_BOOLEAN) && PHP_SAPI !== "cli")
{
    Debugger::enable(Debugger::DEVELOPMENT, storage_path('logs'));
    Debugger::timer();

    if (file_exists($directory = app_path("src/Debugbars")))
    {
        $debugbar_files = get_files($directory);

        if (!empty($debugbar_files))
        {
            $debugbars = array_map(function($debugbar) use($directory) {
                $new_debugbar = str_replace("{$directory}/", "", $debugbar);
                return basename(str_replace("/", "\\", $new_debugbar), ".php");
            }, $debugbar_files);

            foreach ($debugbars as $debugbar) {
                $classPanel = get_app_namespace() . "Debugbars\\{$debugbar}";
                Debugger::getBar()->addPanel(new $classPanel);
            }
        }
    }
}
