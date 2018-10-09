<?php

declare(strict_types=1);
/**
 * Re:箱庭諸島
 * @copyright 2017 Re:箱庭諸島
 */
define("VERSION", "0.0.1");
define("WINDOWS", defined("PHP_WINDOWS_VERSION_MAJOR"));

/**
 * Debug mode:
 * If the server running as cli-server, "DEBUG" is always true.
 * cf. "Router.php".
 */
if (!defined("DEBUG")) {
    define("DEBUG", false);
}

/**
 * Global settings
 */
date_default_timezone_set("Asia/Tokyo");
ini_set("default_charset", "UTF-8");
$ISLAND_TURN;
define("LOCK_RETRY_COUNT", 10);
define("READ_LINE", 1024);
define("DS", DIRECTORY_SEPARATOR);
// [Common directories]
define("DOCROOT", __DIR__.DS);
define("APPPATH", realpath(__DIR__.DS."app".DS).DS);
define("CONTROLLERPATH", realpath(APPPATH.DS."controller".DS).DS);
define("HELPERPATH", realpath(APPPATH.DS."helper".DS).DS);
define("MODELPATH", realpath(APPPATH.DS."model".DS).DS);
define("PRESENTER", realpath(APPPATH.DS."presenter".DS).DS);
define("VIEWS", realpath(APPPATH.DS."views".DS).DS);

// Composer/Autoloader
require_once __DIR__."/vendor/autoload.php";

// Common requires.
use \Hakoniwa\Init;
use \Hakoniwa\Helper\Util;

// require_once HELPERPATH."Enum.php";
