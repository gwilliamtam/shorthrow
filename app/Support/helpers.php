<?php

function ddd(...$args)
{
    $trace = debug_backtrace();
    $source = $trace[0];
    $file = $source['file'];
    if(strstr(base_path(), $file)>=0){
        $file = str_replace(base_path(), '', $file);
    }

    $postedFrom = "Posted from: " . $file . " line:" . $source['line'];

    dd($postedFrom, ...$args);
}
