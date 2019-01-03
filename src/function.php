<?php

declare(strict_types=1);

function println(...$strs): void
{
    foreach ($strs as $str) {
        echo $str;
    }
    echo PHP_EOL;
}



/**
 * 文字列 $str が $prefix から始まるかどうかを返す
 * @param  string $str    検索対象
 * @param  mixed  $prefix 検索したい文字列・文字列の配列
 * @return bool
 */
function startsWith(string $str, mixed $prefix): bool
{
    $type = gettype($prefix);
    if ($type === "string") {
        return mb_substr($str, 0, mb_strlen($prefix)) === $prefix;
    } else if ($type === "array") {
        return (0 !== count(array_filter($prefix, function($p) {
            return mb_substr($str, 0, mb_strlen($p)) === $p;
        })));
    }

    return false;
}
