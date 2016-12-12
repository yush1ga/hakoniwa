<?php
header('Content-Type: application/octet-stream');
header('X-Content-Type-Options: nosniff');
ini_set('display_errors', 1);
error_reporting(E_ALL);


function _dirName($path, $levels = 1) {
	if(phpversion()<7){
		while ($levels--) {
			$path = dirname($path);
		}
		return $path;
	}else{
		return dirname($path,$levels);
	}
}
require_once realpath(_dirName(__FILE__,3)).DIRECTORY_SEPARATOR.'config.php';

$init = new Init();

$filePath = DOCROOT.DIRECTORY_SEPARATOR.$init->dirName.DIRECTORY_SEPARATOR.$init->logname;
$file = file("{$filePath}", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$parsedFile = [];
if($file!==false){
// if(false){
	foreach ($file as $value) {
		list($datetime, $islId, $islName, $deptIp, $deptName) = explode(',', $value);
		list($date, $time) = explode(' ', $datetime);

		$deptIp = "127.0.0.1"; $deptName = "(Test mode)";

		$parsedFile["data"][] = compact('date', 'time', 'islId', 'islName', 'deptIp', 'deptName');
	}

}else{
	$parsedFile["error"] = [
		"code"    => "400",
		"message" => "ファイルの受信に失敗しました"
	];
}

print_r(json_encode($parsedFile));
