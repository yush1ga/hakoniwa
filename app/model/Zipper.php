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

        @gc_collect_cycles();
        @gc_mem_caches();

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
                $src_rel_dir = mb_substr($src_dir, mb_strlen($root_dir));
                $this->zip
                    ->addDirRecursive($src_dir, "hakoniwa/data/")
                    ->deleteFromGlob("**.zip")
                    ->deleteFromGlob("test*")
                    ->addFile(ROOT."config.php", "hakoniwa/config.php")
                    ->addFile(ROOT."hako-init.php", "hakoniwa/hako-init.php")
                    ->addFromString("hakoniwa/LICENSE.html", file_get_contents("https://www.gnu.org/licenses/agpl-3.0-standalone.html"))
                    ->addFromString("hakoniwa/README.html", $this->md2html(ROOT."README.md"));
            }
            $anon_u = _::get_anonymous_usage_arr();
            $g_data = array_merge(["GAME_TITLE" => $this->init->title], $_SERVER);

            $this->zip
                ->addFromString("hakoniwa/phpinfo.html", $anon_u["phpinfo"])
                ->addFromString("hakoniwa/__DONT_DELETE_THIS_FILE__", _::arr2ini($g_data))
                ->saveAsFile(sys_get_temp_dir().DS.$this->zip_name);
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e->message, $e->code, $e);
        }

        return $this;
    }



    public function extractTo(string $zip_path, string &$extract_to = null)
    {
        try {
            $extract_to = $extract_to ?? $this->mkdir_tmp();
            $this->zip
                ->openFile($this->parse_path($zip_path))
                ->deleteFromGlob("**/{phpinfo,README,LICENSE}.html")
                ->extractTo($this->parse_path($extract_to));
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \PhpZip\Exception\ZipException();
        }

        return $this;
    }



    public function restore(string $zipfile_path, string $dataset_path): void
    {
        $tmp_dir = $this->mkdir_tmp();

        try {
            $this->zip
                ->open($zipfile_path)
                ->deleteFromGlob("**/{phpinfo,README,LICENSE}.html")
                ->extractTo($tmp_dir);
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e->message, $e->code, $e);
        }
        $serv = parse_ini_file($tmp_dir.DS."__DONT_DELETE_THIS_FILE__");
        unlink($tmp_dir.DS."__DONT_DELETE_THIS_FILE__");
        $this->cp_a($tmp_dir, ROOT.DS.$this->init->dirName.$serv["REQUEST_TIME"]);
    }



    public function saveTo(string $path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("IAE: `{$path}`");
        }

        try {
            $this->zip
                ->saveAsFile($path.DS.$this->zip_name);
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e->message, $e->code, $e);
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

            throw new \Exception($e->message, $e->code, $e);
        }

        return $this;
    }



    private function md2html(string $path): string
    {
        $md = new \cebe\markdown\GithubMarkdown;
        $md->html5 = true;
        $md->enableNewlines = true;
        $html = $md->parse(file_get_contents($path));

        return <<<EOL
<!doctype html>
<html lang="ja">
<head>
<meta encoding="utf-8">
<title>Read me</title>
</head>
<body>
$html
</body>
</html>

EOL;
    }
}
