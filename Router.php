<?php

declare(strict_types=1);
/**
 * Router.php
 *
 * 開発中(PHPのビルトインCLIサーバーから読込が発生した場合)は常時DEBUG=trueになる。
 */
if (php_sapi_name() === "cli-server") {
    if (!defined("DEBUG")) {
        define("DEBUG", true);
    }
    function dump(...$var): void
    {
        $str = "";
        foreach ($var as $v) {
            ob_start();
            var_dump($v);
            $str .= ob_get_contents();
            ob_end_clean();
        }
        echo "<pre>".PHP_EOL;
        echo htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
        echo "</pre>".PHP_EOL;
    }
    function dump_r(...$var): void
    {
        $str = "";
        foreach ($var as $v) {
            ob_start();
            if (is_array($v) || is_object($v)) {
                print_r($v);
            } else {
                echo $v."\n";
            }
            $str .= ob_get_contents();
            ob_end_clean();
        }
        echo "<pre>".PHP_EOL;
        echo htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
        echo "</pre>".PHP_EOL;
    }
    function console_log(string $str): void
    {
        error_log($str, 0);
    }
    function console_dir($var): void
    {
        ob_start();
        var_dump($var);
        $str = ob_get_contents();
        console_log($str);
        ob_end_clean();
    }

    ini_set("display_errors", "1");
    set_time_limit(0);
    error_reporting(E_ALL);
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Expires: Sat, 01 Apr 2017 09:00:00 GMT");

    return false;
}
