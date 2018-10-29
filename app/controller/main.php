<?php

require_once realpath(__DIR__.'/../../').'/config.php';
require_once MODEL.'hako-file.php';
require_once MODEL.'hako-cgi.php';

/**
 * 箱庭諸島 S.E
 * @author hiro <@hiro0218>
 */
class Main
{
    /**
     * トップページ
     */
    public function execute(): void
    {
        global $init;

        $hako = new \Hako;
        $cgi  = new \Cgi;

        $cgi->parseInputData();
        $cgi->getCookies();

        // 管理パスワード・データファイル存在確認
        if (!file_exists($init->passwordFile) || !$hako->readIslands($cgi)) {
            HTML::header();
            HakoError::noDataFile();
            println('<p><a href="./hako-mente.php">→初期設定</a></p>');
            HTML::footer();
            exit;
        }

        // ファイルロック失敗時、強制終了
        if (false === ($lock = Util::lock())) {
            exit;
        }

        $cgi->setCookies();

        if (mb_strtolower($cgi->dataSet['DEVELOPEMODE'] ?? '') == 'javascript') {
            $html = new HtmlMapJS;
            $com  = new MakeJS;
        } else {
            $html = new HtmlMap;
            $com  = new Make;
        }
        switch ($cgi->mode) {
            case "log":
                $html = new HtmlTop;
                $html->header();
                $html->log();
                $html->footer();

                break;

            case "turn":
                $turn = new Turn;
                $html = new HtmlTop;
                $html->header();
                // ターン処理後、通常トップページ描画
                $turn->turnMain($hako, $cgi->dataSet);
                $hako->readIslands($cgi);
                $html->main($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "owner":
                $html->header();
                $html->owner($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "command":
                $html->header();
                $com->commandMain($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "new":
                $html->header();
                $com->newIsland($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "comment":
                $html->header();
                $com->commentMain($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "print":
                $html->header();
                $html->visitor($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "targetView":
                $html->head();
                $html->printTarget($hako, $cgi->dataSet);
                //$html->footer();
                break;

            case "change":
                $html->header();
                $com->changeMain($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "ChangeOwnerName":
                $html->header();
                $com->changeOwnerName($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "conf":
                $html = new HtmlTop;
                $html->header();
                $html->register($hako, $cgi->dataSet);
                $html->footer();

                break;

            case "changeInfo":
                if (($cgi->dataSet["PreCheck"] ?? "") === "true") {
                    header('Content-Type:text/plain;charset=utf-8');
                    if (Util::checkPassword("", base64_decode($cgi->dataSet["Pwd"] ?? "", true))) {
                        echo "true";
                    } else {
                        echo "false";
                    }

                    break;
                }
                require_once MODEL."hako-log.php";
                (new LogIO)->write_noticefile($hako, $cgi->dataSet);
                /*. missing_break; .*/
                // no break
            default:
                $html = new HtmlTop;
                $html->header();
                $html->main($hako, $cgi->dataSet);
                $html->footer();
        }
        Util::unlock($lock);
        exit;
    }
}
