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
        'mbstring'
    ];
    private function HasExtension(string $extName): bool
    {
        return extension_loaded($extName);
    }
    private function chkExtensionLoaded()
    {
        foreach ($this->needExts as $ext) {
            if (!$this->HasExtension($ext)) {
                echo 'ご利用のPHPサーバー内にて、本プログラムの動作に必要なモジュール"',$ext,'"が読み込まれていないため、動作を停止しました。<br>大変お手数ですが、サーバー管理者にお問合せください。';
                //-> php.iniから'module.so'/'php_module.dll'を有効にする
                die;
            }
        }
    }
    public function __construct()
    {
        $this->chkExtensionLoaded();

        try {
            $tmp = random_int(0, 1);
        } catch (Exception $e) {
            echo $e;
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
    <title>{$title}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css" integrity="sha256-/Z28yXtfBv/6/alw+yZuODgTbKZm86IKbPE/5kjO/xY=" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.js" integrity="sha256-Bhi6GMQ/72uYZcJXCJ2LToOIcN3+Cx47AZnq/Bw1f7A=" crossorigin="anonymous"></script>
  </head>
  <body>
END;
    }
    private function view_foot()
    {
        echo <<<END
  </body>
</html>
END;
    }
}

new LaunchTest();
