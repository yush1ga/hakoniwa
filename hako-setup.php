<?php

declare(strict_types=1);

namespace Hakoniwa;

if (php_sapi_name() !== "cli") {
    exit;
}

require_once "config.php";

/**
 * Setup
 */
final class Setup
{
    use \Hakoniwa\Model\FileIO;

    private $head_tmp;
    private $current_tmp;

    public function __construct()
    {
        $this->init = new \Hakoniwa\Init;
    }



    private function update_main(...$argv): void
    {
        echo "[UPDATE_MAIN]\n";
        print_r($argv);
        file_put_contents("test.txt", implode("\r\n", $argv));
        sleep(20);
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
         * -[] fork process (and change cwd)
         * -[] compare
         * -[] flush
         * -[] copy
         * -[] velify (=compare?)
         * -[x] rimraf(tmp)
         */

        // mkdir(tmp)
        echo "MKDIR...";
        $this->head_tmp = $this->mkdir_tmp();
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
        echo "backup...";
        $this->current_tmp = $this->mkdir_tmp();
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }

        // - copy
        echo "copy..";
        $this->cp_a(DOCROOT, $this->current_tmp);

        // - verify
        echo "verify...";
        if (!$this->is_same(DOCROOT, $this->current_tmp)) {
            throw new \RuntimeException(
                <<<EOL

                [Err] Copy files failed, please check below.
                - File/Directory Permissions
                - Arguments

EOL
            );
        }
        echo "done.\n";

        // fork
        $d = DOCROOT;
        if (WINDOWS) {
            chdir($this->head_tmp);
            // $p = popen("start \"\" php hako-setup.php \"{$d}\" \"{$this->current_tmp}\" \"{$this->head_tmp}\"", "r");
            $p = popen('start "" php -r \'$argv = ["main", "'.$d.'", "'.$this->current_tmp.'", "'.$this->head_tmp.'"]; require hako-setup.php;\'', "r");
            pclose($p);
        } else {
            try {
                exec("nohup php hako-setup.php \"{$d}\" \"{$this->current_tmp}\" \"{$this->head_tmp}\" >/dev/null 2>&1 &");
            } catch (\Throwable $e) {
                error_log($e->getMessage(), 0);
                die;
            }
        }


        // rimraf(tmp)
        // $this->rimraf($this->head_tmp);
        // $this->rimraf($this->current_tmp);
    }

    private function check_version(): void
    {
    }
}

$setup = new Setup();

if ($argv[1] === "main") {
    $setup->update_main($argv);
} elseif ($argv[0] === "hako-setup.php") {
    $setup->update();
}

// if (isset($argc, $argv)) {
//     print_r($argv);
//     if (@$argv[1] !== DOCROOT) {
//         (new Setup)->update();
//     } else if ($argc !== 4) {
//         print_r($argv);
//         throw new \InvalidArgumentException("You must be set 4 arguments, but you actual set {$argc} arguments.");
//     } else {
//         print_r($argv);
//         (new Setup)->update_main($argv);
//     }
// }
