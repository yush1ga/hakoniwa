<?php

declare(strict_types=1);

namespace Hakoniwa\Admin\Maintenance;

require_once __DIR__."/../../../config.php";

require_once MODEL.'/admin.php';

use \HakoError;

/**
 * 箱庭諸島 S.E
 * @author hiro <@hiro0218>
 */

class Mente extends \Admin
{
    use \Hakoniwa\Model\FileIO;

    public function __construct()
    {
        $html = new \HtmlMente;
        $cgi = new \Cgi;
        $this->parseInputData();
        $cgi->getCookies();

        $this->dataSet['PASSWORD'] = $this->dataSet['PASSWORD'] ?? '';
        $html->header();

        switch ($this->mode) {
            case "NEW":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->newMode();
                }
                $html->main($this->dataSet);

                break;

            case "CURRENT":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->currentMode($this->dataSet['NUMBER']);
                }
                $html->main($this->dataSet);

                break;

            case "DELETE":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->delMode($this->dataSet['NUMBER']);
                }
                $html->main($this->dataSet);

                break;

            case "NTIME":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->timeMode();
                }
                $html->main($this->dataSet);

                break;

            case "STIME":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->stimeMode($this->dataSet['SSEC']);
                }
                $html->main($this->dataSet);

                break;

            case "setup":
                $this->setupMode();
                $html->enter();

                break;

            case "enter":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $html->main($this->dataSet);
                }

                break;

            default:
                $html->enter();

                break;
        }

        $html->footer();
    }

    public function newMode(): void
    {
        global $init;

        $now = $_SERVER['REQUEST_TIME'];
        $now -= $now % $init->unitTime;

        // 総合データファイル
        $fileName = $init->dirName.'/hakojima.dat';
        $txt = <<<EOM
1
$now
0
1

EOM;
        file_put_contents($fileName, $txt);

        // 同盟ファイル生成
        touch($init->dirName.'/ally.dat');

        // アクセスログ生成
        touch($init->dirName.'/'.$init->logname);

        // .htaccess生成
        $fileName = $init->dirName.'/.htaccess';
        file_put_contents($fileName, "Options -Indexes\n", LOCK_EX);
        chmod($fileName, 0644);
    }



    public function delMode($id): void
    {
        global $init;

        $dirName = strcmp($id, "") == 0 ? $init->dirName : $init->dirName.".bak$id";
        $this->rimraf($dirName);
    }

    public function timeMode(): void
    {
        $date = $this->dataSet['date'].' '.$this->dataSet['time'];
        $date = date_parse_from_format('Y-m-d H:i', $date);
        if (!checkdate($date['month'], $date['day'], $date['year'])) {
            throw new \InvalidArgumentException("指定された日付が不正", 1);
        }
        $toSetDate = mktime($date['hour'], $date['minute'], 0, $date['month'], $date['day'], $date['year']);
        $this->stimeMode($toSetDate);
    }

    public function stimeMode($sec): void
    {
        global $init;

        $fileName = $init->dirName.'/hakojima.dat';
        $fp = fopen($fileName, "r+");
        $buffer = [];
        while (false !== ($line = fgets($fp, READ_LINE))) {
            array_push($buffer, $line);
        }
        $buffer[1] = "$sec\n";
        fseek($fp, 0);
        while (null !== ($line = array_shift($buffer))) {
            fwrite($fp, $line);
        }
        fclose($fp);
    }

    public function currentMode($id): void
    {
        global $init;

        $this->rimraf($init->dirName);
        $this->cp_a($init->dirName.".bak{$id}/", $init->dirName);
    }

    public function setupMode()
    {
        global $init;

        function is_same_string(string $s1, string $s2): bool
        {
            return $s1 !== "" && $s2 !== "" && strcmp($s1, $s2) === 0;
        }

        $invalid = false;

        if (!is_same_string($this->dataSet['MPASS1'], $this->dataSet['MPASS2'])) {
            \HakoError::wrongMasterPassword();

            $invalid = true;
        }
        if (!is_same_string($this->dataSet['SPASS1'], $this->dataSet['SPASS2'])) {
            \HakoError::wrongSpecialPassword();

            $invalid = true;
        }

        if ($invalid) {
            return;
        }

        if (is_same_string($this->dataSet['MPASS1'], $this->dataSet['SPASS1'])) {
            \HakoError::necessaryBeSetAnotherPassword();

            return;
        }

        $masterPasswd  = \Util::encode($this->dataSet['MPASS1'], false);
        $specialPasswd = \Util::encode($this->dataSet['SPASS1'], false);
        $fp = fopen($init->passwordFile, "w");
        fwrite($fp, "$masterPasswd\n");
        fwrite($fp, "$specialPasswd\n");
        fclose($fp);
    }
}
