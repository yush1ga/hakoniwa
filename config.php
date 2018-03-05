<?php
/**
 * Re:箱庭諸島SE
 * @copyright 2017 Re:箱庭諸島SE
 */

/**
 * Debug mode:
 * If the server running as cli-server, 'DEBUG' is always true.
 */
if (!defined('DEBUG')) {
    define('DEBUG', false);
}

/**
 * Global settings
 */
ini_set('default_charset', 'UTF-8');
date_default_timezone_set('Asia/Tokyo');
$ISLAND_TURN;
define("LOCK_RETRY_COUNT", 10);
define("LOCK_RETRY_INTERVAL", 1000);
define("READ_LINE", 1024);
// [Common directories]
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);
define('APPPATH', realpath(__DIR__.'/app/').DIRECTORY_SEPARATOR);
define('CONTROLLERPATH', realpath(APPPATH.'/controller/').DIRECTORY_SEPARATOR);
define('HELPERPATH', realpath(APPPATH.'/helper/').DIRECTORY_SEPARATOR);
define('MODELPATH', realpath(APPPATH.'/model/').DIRECTORY_SEPARATOR);
define('PRESENTER', realpath(APPPATH.'/presenter/').DIRECTORY_SEPARATOR);
define('VIEWS', realpath(APPPATH.'/views/').DIRECTORY_SEPARATOR);

// Composer/Autoloader
require_once __DIR__.'/vendor/autoload.php';

if (DEBUG) {
    ini_set('display_errors', 1);
    set_time_limit(0);
    error_reporting(E_ALL);
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Expires: Sat, 01 Apr 2017 09:00:00 GMT");
    require_once __DIR__.'/launchTest.php';
}

// Common requires.
require_once DOCROOT.'hako-init.php';
require_once HELPERPATH.'util.php';
