<?php
/**
 * Router.php
 *
 * 開発中(PHPのビルトインCLIサーバーから読込が発生した場合)は常時DEBUG=trueになる。
 * ほんまか？
 */
if (php_sapi_name() === 'cli-server') {
    if (!defined('DEBUG')) {
        define('DEBUG', true);
    }
}

return false;
