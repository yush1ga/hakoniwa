<?php

function println(...$strs): void
{
    foreach ($strs as $str) {
        echo $str;
    }
    echo PHP_EOL;
}
