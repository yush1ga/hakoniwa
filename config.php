<?php
/**
 * 箱庭諸島 S.E - 初期設定用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

// set default charset / TimeZone settings
ini_set('default_charset', 'UTF-8');
date_default_timezone_set('Asia/Tokyo');


// 箱庭の設定
$ISLAND_TURN;
define("DEBUG", false); // true: デバッグ false: 通常
define("LOCK_RETRY_COUNT", 10);
define("LOCK_RETRY_INTERVAL", 1000);
define("READ_LINE", 1024);


// 開発用の設定
if (DEBUG) {
    ini_set('display_errors', 1);
    set_time_limit(0);
    error_reporting(E_ALL);
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");   // 過去の日付
}


// 各種ディレクトリ
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);  // ドキュメントルート
define('APPPATH', realpath(__DIR__.'/app/').DIRECTORY_SEPARATOR);  // アプリケーションディレクトリ
define('CONTROLLERPATH', realpath(APPPATH.'/controller/').DIRECTORY_SEPARATOR);  // コントローラ
define('HELPERPATH', realpath(APPPATH.'/helper/').DIRECTORY_SEPARATOR);  // ヘルパー
define('MODELPATH', realpath(APPPATH.'/model/').DIRECTORY_SEPARATOR);  // モデル
define('PRESENTER', realpath(APPPATH.'/presenter/').DIRECTORY_SEPARATOR);  // プレゼンター
define('VIEWS', realpath(APPPATH.'/views/').DIRECTORY_SEPARATOR);  // ビュー


// 共通
require_once DOCROOT.'hako-init.php';
require_once APPPATH.'helper/util.php';
