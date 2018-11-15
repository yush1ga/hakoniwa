<?php

declare(strict_types=1);

namespace Hakoniwa\Utility;

require_once realpath(__DIR__."/../../config.php");

final class Zipper
{
    use \Hakoniwa\Model\FileIO;

    protected $zip;
    public function __construct()
    {
        $this->zip = new \PhpZip\ZipFile();
    }
    public function __destruct()
    {
        $this->zip->close();
    }
    public function close(): void
    {
        $this->zip->close();
    }

    public function backup_playdata(string $dir, bool $verbose)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("IAE: `{$dir}`");
        }

        try {
            $this->zip
                ->addDirRecursive($dir, "data/", \PhpZip\ZipFile::METHOD_STORED)
                ->deleteFromGlob("test*")
                ->addFile(ROOT."LICENSE", "LICENSE.txt");

            if ($verbose) {
                $this->zip
                    ->addFile(ROOT."config.php", "config.php")
                    ->addFile(ROOT."hako-init-default.php", "hako-init-default.php")
                    ->addFile(ROOT."hako-init.php", "hako-init.php");
            }
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
                ->saveAsFile("hakoniwa.zip");
        } catch (\PhpZip\Exception\ZipException $e) {
            $this->zip->close();

            throw new \Exception($e);
        }
        $this->rimraf("hakoniwa.zip");

        return $this;
    }
}
