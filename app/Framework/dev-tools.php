<?php

function d($var)
{
    dump($var);
}

function de($var)
{
    dump($var);
    exit;
}

function loadingTimeCheck()
{
    if (!(defined('DOING_AJAX') && DOING_AJAX)) {
        ?><div style="position:fixed;bottom:0;right:0;padding:0.2em;background:#fff;border:1px solid #ccc;border-radius:4px;"><?php
        timer_stop(1);
        echo " | ";
        $unit = array('b','kb','mb','gb','tb','pb');
        $size = memory_get_usage(true);
        echo @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
        ?></div><?php
    }
}

add_action('shutdown', 'loadingTimeCheck');