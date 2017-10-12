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
if(!extension_loaded('mbstring')){
    echo 'ご利用のPHPサーバー内にて、モジュール"mbstring"が読み込まれていないため、本プログラムは動作を停止しました。<br>大変お手数ですが、サーバー管理者にお問合せください。';
    //-> php.iniから'mbstring.so'/'php_mbstring.dll'を有効にする
    die;
}



// 箱庭の設定
$ISLAND_TURN;
define("DEBUG", true); // true: デバッグ false: 通常
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
    require_once __DIR__.DIRECTORY_SEPARATOR.'ChromePhp.php'; // ChromeLogger
    // ChromePhp::log();
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
require_once HELPERPATH.'util.php';
