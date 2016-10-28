<?php
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
	foreach ($file as $value) {
		list($datetime, $islId, $islName, $deptIp, $deptHostName) = explode(',', $value);
		list($date, $time) = explode(' ', $datetime);
		$parsedFile["data"][] = compact('date', 'time', 'islId', 'islName', 'deptIp', 'deptHostName');
	}

}else{
	$parsedFile["error"] = [
		"code"    => "400",
		"message" => "ファイルの受信に失敗しました"
	];
}

print_r(json_encode($parsedFile));
