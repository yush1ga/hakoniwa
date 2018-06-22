<?php
/**
 * Router.php
 *
 * 開発中(PHPのビルトインCLIサーバーから読込が発生した場合)は常時DEBUG=trueになる。
 */
if (php_sapi_name() === 'cli-server') {
    if (!defined('DEBUG')) {
        define('DEBUG', true);
    }
    function dump($var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

return false;
