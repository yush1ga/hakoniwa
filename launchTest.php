<?php

namespace Hakoniwa\Test;

require_once 'ChromePhp.php';// ChromeLogger
// ChromePhp::log($hoge);
require_once 'hako-init.php';

use \ChromePhp as Console;

/**
*
*/
class LaunchTest extends \Init
{
    private $needExts = [
        'mbstring',
        'zip'
    ];
    private function HasExtension(string $extName): bool
    {
        return extension_loaded($extName);
    }

    private function chkExtensionLoaded()
    {
        $noHaveExt = '';
        foreach ($this->needExts as $ext) {
            if (!$this->HasExtension($ext)) {
                $noHaveExt .= ", $ext";
                //-> php.iniから'$ext.so'/'php_$ext.dll'を有効にする
            }
        }
        if ($noHaveExt !== "") {
            $noHaveExt = substr($noHaveExt, 2);

            throw new \Error("動作に必要なモジュール\"$noHaveExt\"が読み込まれていません。");
        }
    }
    public function __construct()
    {
        try {
            $this->chkExtensionLoaded();
            random_int(0, 1);
        } catch (\Throwable $e) {
            $this->view_head();
            $this->print_nl2br($e->getMessage());
            $this->view_foot();
            die;
        }
    }

    private function view_head()
    {
        echo <<<END
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="x-uja-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>$this->title 動作テスト</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.css" integrity="sha256-oDCP2dNW17Y1QhBwQ+u2kLaKxoauWvIGks3a4as9QKs=" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha256-3edrmyuQ0w65f8gfBsqowzjJe2iM6n0nKciPUp8y+7E=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js" integrity="sha256-RKNmL9+6j/3jB72OcIg8OQr91Bi4OgFPnKQOFS1O+fo=" crossorigin="anonymous"></script>
<style type="text/css">body{background-color:#dadada;}body > .grid{height:100%;}.image{margin-top:-100px;}.column{max-width:78vw;}</style>
</head>
<body>
<div class="ui middle aligned center aligned grid">
  <div class="column">
    <h2 class="ui teal image header">
        <img src="$this->imgDir/monster1.gif" class="image">
        <div class="content">動作テスト</div>
    </h2>
    <div class="ui stacked left aligned segment">
        <p>ご利用のPHPサーバー内にて以下のエラーが発生したため、動作を停止しました。<br>大変お手数ですが、サーバー管理者にお問合せください。</p>
    </div>

END;
    }
    private function view_foot()
    {
        echo <<<END
    </div>
</div>
</body>
</html>

END;
    }

    public function print_nl2br($str)
    {
        echo '<div class="ui stacked left aligned segment">', $str, "</div>\n";
    }

    public function print_debug(...$args)
    {
        $f = $this->HasExtension('Xdebug');
        foreach ($args as $arg) {
            if (!$f) {
                echo "<pre>";
            }
            var_dump($arg);
            if (!$f) {
                echo "</pre>\n";
            }
        }
    }
}

new LaunchTest;
