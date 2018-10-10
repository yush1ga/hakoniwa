<?php

declare(strict_types=1);

namespace Hakoniwa;

// if (php_sapi_name() !== 'cli') {
//     header('HTTP/1.0 403 Forbidden', true, 403);
//     exit;
// }

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



    public function update(): void
    {
        /**
         * -[x] mkdir(tmp)
         * -[x] git clone HEAD /tmp
         * -[] backup
         *   +[] mkdir_tmp
         *   +[] copy
         *   +[] verify
         * -[] compare
         * -[] flush
         * -[] copy
         * -[] velify (=compare?)
         * -[x] rimraf(tmp)
         */

        // mkdir(tmp)
        $this->head_tmp = $this->mkdir_tmp();
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }

        // git clone HEAD /tmp
        $clone_branch = DEBUG ? "develop" : "master";
        $stdout = [];
        exec("git --version", $stdout);
        if (!preg_match("/^git version .*$/", $stdout[0])) {
            throw new \ErrorException("[Err] Do you have `git`?");
        }
        unset($stdout);
        exec("git clone --quiet --depth 1 --branch {$clone_branch} https://github.com/Sotalbireo/hakoniwa.git {$this->head_tmp}");

        // backup
        // - mkdir_tmp
        $this->current_tmp = $this->mkdir_tmp();
        if ($this->head_tmp === false) {
            throw new \RuntimeException("[Err] You didn't mkdir on System Tempolary Directory.");
        }

        // - copy
        $this->cp_a(DOCROOT, $this->current_tmp);

        // - verify
        if (!$this->is_same(DOCROOT, $this->current_tmp)) {
            throw new \RuntimeException(<<<EOL
                [Err] Copy files failed, please check below.
                - File/Directory Permissions
                - Arguments
EOL
            );
        }


        // rimraf(tmp)
        $this->rimraf($this->head_tmp);
    }

    private function check_version(): void
    {
    }
}

(new Setup)->update();
