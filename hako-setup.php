<?php
/**
 * Re:箱庭諸島
 * @copyright 2017 Re:箱庭諸島, CGIゲーム保存会
 */
declare(strict_types=1);

if (php_sapi_name() !== "cli") {
    exit;
}
date_default_timezone_set("Asia/Tokyo");
ini_set("default_charset", "UTF-8");
ini_set("mbstring.language", "Japanese");

if (!defined("WINDOWS")) {
    define("WINDOWS", defined("PHP_WINDOWS_VERSION_MAJOR"));
}
if (!defined("DEBUG")) {
    define("DEBUG", true);
}

require "app/model/FileIO.php";

use \Hakoniwa\InitDefault as iniD;



/**
 * Setup
 */
final class Setup
{
    use \Hakoniwa\Model\FileIO;

    const DOCROOT = __DIR__;
    private $head_tmp;
    private $current_tmp;

    public function __construct()
    {
        $this->head_tmp = $this->mkdir_tmp();
        $this->current_tmp = $this->mkdir_tmp();
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
         * -[] compare
         *   +[] get new config list
         *   +[] get current config list
         *   +[] compare
         * -[] overwrite
         *   +[] flush
         *   +[] copy
         *   +[] velify
         * -[x] rimraf(tmp)
         */

        // mkdir(tmp)
        echo "MKDIR...";
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }
        echo "done.\n";

        // git clone HEAD /tmp
        echo "git clone...";
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
        echo "backup..";
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }

        // - copy
        echo "copy..";
        $this->cp_a(self::DOCROOT, $this->current_tmp);

        // - verify
        echo "verify...";
        if (!$this->is_same(self::DOCROOT, $this->current_tmp)) {
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
        // - get new config list
        $version_new = file_get_contents($this->parse_path($this->head_tmp."/config.php"));
        require $this->parse_path($this->head_tmp."/hako-init-default.php");
        $confs_new = (new \ReflectionClass(iniD::class))->getDefaultProperties();

        // - get current config list
        $version_current = file_get_contents($this->parse_path($this->current_tmp."/config.php"));
        require $this->parse_path($this->current_tmp."/hako-init.php");
        $confs_current = (new \ReflectionClass(\Hakoniwa\init::class))->getDefaultProperties();

        // - compare

        // rimraf(tmp)
        $this->rimraf($this->head_tmp);
        $this->rimraf($this->current_tmp);
    }

    private function check_version(): void
    {
    }
}





(new Setup)->update();
