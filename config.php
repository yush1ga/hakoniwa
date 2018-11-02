<?php

declare(strict_types=1);
/**
 * Re:箱庭諸島
 * @copyright 2017 Re:箱庭諸島
 */
define("VERSION", "0.0.1");
defined("WINDOWS") || define("WINDOWS", defined("PHP_WINDOWS_VERSION_MAJOR"));

/**
 * Debug mode:
 * If the server running for test as cli-server, "DEBUG" is always true.
 * cf. "Router.php".
 */
defined("DEBUG") || define("DEBUG", false);

/**
 * Global settings
 */
date_default_timezone_set("Asia/Tokyo");
ini_set("default_charset", "UTF-8");
ini_set("mbstring.language", "Japanese");
$ISLAND_TURN;
define("LOCK_RETRY_COUNT", 10);
define("READ_LINE", 1024);
define("DS", DIRECTORY_SEPARATOR);
// [Common directories]
define("ROOT", __DIR__.DS);
define("APP", realpath(__DIR__.DS."app".DS).DS);
define("CONTROLLER", realpath(APP.DS."controller".DS).DS);
define("HELPER", realpath(APP.DS."helper".DS).DS);
define("MODEL", realpath(APP.DS."model".DS).DS);
define("PRESENTER", realpath(APP.DS."presenter".DS).DS);
define("VIEWS", realpath(APP.DS."views".DS).DS);

// LaunchTest
require_once __DIR__."/LaunchTest.php";

// Composer/Autoloader
if (mb_substr(__DIR__, 0, mb_strlen(sys_get_temp_dir())) !== sys_get_temp_dir() && is_file("vendor/autoload.php")) {
    require_once "vendor/autoload.php";
}

// Common requires.
use \Hakoniwa\Helper\Util;

// require_once HELPER."Enum.php";
