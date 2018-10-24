<?php

declare(strict_types=1);

ini_set('display_errors', 1);
set_time_limit(0);
error_reporting(E_ALL);
require_once 'Utility.php';

// if (!Utility::isAjax()) {
// 	header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other', true, 303);
// 	header("Location: https://www.google.com/teapot");
// 	die;
// }
header("Content-type: application/json; charset=UTF-8");
echo "echo";
