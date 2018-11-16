<?php

declare(strict_types=1);

namespace Hakoniwa\Utility;

require_once realpath(__DIR__."/../../config.php");

use \Util as _;

final class Zipper
{
    use \Hakoniwa\Model\FileIO;

    protected $init;
    protected $zip;
    protected $zip_name;

    public function __construct(string $file_name)
    {
        global $init;

        ini_set("max_execution_time", "300");
        ini_set("memory_limit", "-1");

        gc_collect_cycles();
        gc_mem_caches();

        $this->init = $init;
        $this->zip = new \PhpZip\ZipFile();
        $this->zip_name = $file_name;
    }
    public function __destruct()
    {
        $this->zip->close();
        if (is_file(sys_get_temp_dir().DS.$this->zip_name)) {
            unlink(sys_get_temp_dir().DS.$this->zip_name);
        }

        ini_restore("max_execution_time");
        ini_restore("memory_limit");
    }
    public function close(): void
    {
        $this->__destruct();
    }

    public function backup_localdata(string $src_dir, bool $verbose = false)
    {
        if (!is_dir($src_dir)) {
            throw new \InvalidArgumentException("IAE: `{$src_dir}`");
        }
        $src_dir = $this->parse_path($src_dir);
        $root_dir = $this->parse_path(ROOT);
        if (!_::starts_with($src_dir, $root_dir)) {
            throw new \InvalidArgumentException("ERROR `{$src_dir}` `{$root_dir}`");
        }

        try {
            if ($verbose) {
                $directoryIterator = new \RecursiveDirectoryIterator(ROOT);
                $ignoreIterator = new \PhpZip\Util\Iterator\IgnoreFilesRecursiveFilterIterator(
                    $directoryIterator,
                    [
                        ".git/",
                        "node_modules/",
                        "vendor/"
                    ]
                );
                $this->zip
                    ->addFilesFromIterator($ignoreIterator, "hakoniwa/");
            } else {
                $md_parser = new \cebe\markdown\GithubMarkdown();
                $md_parser->html5 = true;
                $md_parser->enableNewlines = true;
                $src_rel_dir = mb_substr($src_dir, mb_strlen($root_dir));
                $this->zip
                    ->addDirRecursive($src_dir, "hakoniwa/{$src_rel_dir}/")
                    ->addFile(ROOT."config.php", "hakoniwa/config.php")
                    ->addFile(ROOT."hako-init.php", "hakoniwa/hako-init.php")
                    ->deleteFromGlob("test*")
                    ->deleteFromGlob("**.zip")
                    ->addFile(ROOT."LICENSE", "hakoniwa/LICENSE.txt")
                    ->addFromString("hakoniwa/README.html", $md_parser->parse(file_get_contents(ROOT."README.md")));
            }
            $aus = _::get_anonymous_usage_stats();
            $this->zip
                ->addFromString("hakoniwa/phpinfo.html", $aus["phpinfo"])
                ->saveAsFile(sys_get_temp_dir().DS.$this->zip_name);
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e);
        }

        return $this;
    }



    public function restore(): void
    {
    }



    public function save_to(string $path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("IAE: `{$path}`");
        }

        try {
            $this->zip
                ->saveAsFile($path.DS.$this->zip_name);
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e);
        }

        return $this;
    }



    public function download()
    {
        try {
            $this->zip
                ->outputAsAttachment("hakoniwa.zip");
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e);
        }

        return $this;
    }
}
