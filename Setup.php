<?php
/**
 * Re:箱庭諸島
 * @copyright 2017 Re:箱庭諸島, CGIゲーム保存会
 */
declare(strict_types=1);

if (php_sapi_name() !== "cli") {
    header("HTTP/1.0 404 Not Found");
    exit;
}
date_default_timezone_set("Asia/Tokyo");
ini_set("default_charset", "UTF-8");
ini_set("mbstring.language", "Japanese");

defined("WINDOWS") || define("WINDOWS", defined("PHP_WINDOWS_VERSION_MAJOR"));
defined("DEBUG") || define("DEBUG", true);

require "app/model/FileIO.php";



/**
 * Setup
 */
final class Setup
{
    use \Hakoniwa\Model\FileIO;

    const ROOT = __DIR__;
    private $head_tmp;
    private $current_tmp;
    private $rnd;

    public function __construct()
    {
        $this->head_tmp = $this->mkdir_tmp();
        $this->current_tmp = $this->mkdir_tmp();
        $this->rnd = $this->random_str();
    }



    public function update(): void
    {
        /**
         * -[x] mkdir(tmp)
         * -[x] git clone HEAD /tmp
         * -[x] backup
         *   +[] mkdir_tmp
         *   +[] copy
         *   +[] verify
         * -[x] compare
         *   +[] versions
         *   +[] Init compare to InitDefault
         * -[] overwrite
         *   +[] flush
         *   +[] copy
         *   +[] velify
         * -[] install
         * -[x] rimraf(tmp)
         */

        // mkdir(tmp)
        echo "prepare update..\n";
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }

        // git clone HEAD /tmp
        echo "get HEAD data...";
        $clone_branch = DEBUG ? "develop" : "master";
        $stdout = [];
        exec("git --version", $stdout);
        if (!preg_match("/^git version .*$/", $stdout[0])) {
            throw new \ErrorException("[Err] Do you have `git`?");
        }
        unset($stdout);
        exec("git clone --quiet --depth 1 --branch {$clone_branch} https://github.com/Sotalbireo/hakoniwa.git {$this->head_tmp}");
        echo "done.\n";

        // backup
        // - mkdir_tmp
        echo "backup...";
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }

        // - copy
        echo ".";
        $this->cp_a(self::ROOT, $this->current_tmp);

        // - verify
        echo ".";
        if (!$this->is_same(self::ROOT, $this->current_tmp)) {
            throw new \RuntimeException(
                <<<EOL

                [Err] Copy files failed, please check below.
                - File/Directory Permissions
                - Arguments

EOL
            );
        }
        echo "done.\n";

        // compare
        echo "compare...";
        // - versions
        echo ".";
        $ver_h = $ver_c = [];
        $tmp = file_get_contents($this->parse_path($this->head_tmp."/config.php"));
        preg_match("/define\(\"VERSION\", \"(?P<major>\d+)\.(?P<minor>\d+)\.(?P<opt>.*?)\"\);/", $tmp, $ver_h);
        $tmp = file_get_contents($this->parse_path($this->current_tmp."/config.php"));
        preg_match("/define\(\"VERSION\", \"(?P<major>\d+)\.(?P<minor>\d+)\.(?P<opt>.*?)\"\);/", $tmp, $ver_c);
        unset($tmp);

        $key_exists_h = array_key_exists("major", $ver_h) && array_key_exists("minor", $ver_h) && array_key_exists("opt", $ver_h);
        $key_exists_c = array_key_exists("major", $ver_c) && array_key_exists("minor", $ver_c) && array_key_exists("opt", $ver_c);

        if (!$key_exists_h || !$key_exists_c) {
            echo <<<EOT

バージョン表記が不正な値です。
Because version syntax is illegal, abort system.

EOT;
            exit;
        }
        unset($key_exists_h, $key_exists_c);

        if ($ver_h["major"] !== $ver_c["major"]) {
            echo <<<EOT

メジャーバージョンが違うため、アップデートシステムを利用できません。
Because it isn't same major versions, abort system.
current\t: `{$ver_c[1]}.{$ver_c[2]}.{$ver_c[3]}`,
HEAD\t: `{$ver_h[1]}.{$ver_h[2]}.{$ver_h[3]}`.

EOT;
            exit;
        }

        if ((int)$ver_h["minor"] < (int)$ver_c["minor"]) {
            echo <<<EOT

現在稼働中のバージョンのほうが新しいため、システムを中止しました。
Because the using App newer than HEAD's version, abort system.
current\t: `{$ver_c[1]}.{$ver_c[2]}.{$ver_c[3]}`,
HEAD\t: `{$ver_h[1]}.{$ver_h[2]}.{$ver_h[3]}`.

EOT;
        }

        // - Init compare to new initDefault
        echo ".";
        $tmp = file_get_contents($this->parse_path($this->head_tmp."/hako-init-default.php"));
        $tmp2 = str_replace("class InitDefault", "class NewInit", $tmp);
        if ($tmp2 !== null) {
            $this->mkfile(sys_get_temp_dir()."/{$this->rnd}", $tmp2);
        }
        require $this->parse_path(sys_get_temp_dir()."/{$this->rnd}");
        unset($tmp, $tmp2);

        require $this->parse_path($this->current_tmp."/hako-init.php");
        $conf_c = (new \ReflectionClass(\Hakoniwa\init::class))->getDefaultProperties();

        echo ".";
        $mismatch_conf = [];
        foreach ($conf_c as $prop => $v) {
            if (!property_exists(\Hakoniwa\NewInit::class, $prop)) {
                $mismatch_conf[] = "- Obsolate or Wrong property: `\${$prop}`.";
            }
        }
        if ($mismatch_conf !== []) {
            echo "Oops!\nAbort system, because below error(s).\n";
            echo implode("\n", $mismatch_conf);

            exit;
        }
        echo "done.\n";



        // overwrite
        echo "Overwrite...";

        // - flush
        if (!$this->rimraf(self::ROOT)) {
            echo "\nERR! Abort system and Rollback...\n";
            $this->cp_a($this->current_tmp, self::ROOT, true);
        }
        // - copy
        echo "..";
        $this->cp_a($this->head_tmp, self::ROOT);
        // - velify
        echo "...";
        if (!$this->is_same($this->current_tmp, self::ROOT)) {
            throw new \RuntimeException(
                <<<EOL

                [Err] Copy files failed, please check below.
                - File/Directory Permissions
                - Arguments

EOL
            );
        }
        echo "done.\n";




        // install
        exec("npm install");
    }

    public function __destruct()
    {
        // rimraf(tmp)
        echo "\n--\nremove tempolary files..\n1/3..\n";
        $this->rimraf($this->head_tmp);
        echo "2/3..\n";
        // $this->rimraf($this->current_tmp);
        echo "3/3...";
        if (is_file($this->parse_path(sys_get_temp_dir()."/{$this->rnd}"))) {
            $this->rimraf(sys_get_temp_dir()."/{$this->rnd}");
        }
        echo "done.\n";
    }
}





(new Setup)->update();
