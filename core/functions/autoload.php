<?php

$functions = array_filter(glob(__DIR__ . "/*.php"), function($file) {
    return basename($file) !== "autoload.php"; // exclude self
});

foreach ($functions as $fn) {
    require_once $fn;
}
