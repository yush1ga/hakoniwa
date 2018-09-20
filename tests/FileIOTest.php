<?php

declare(strict_types=1);

use \PHPUnit\Framework\TestCase;
use \Hakoniwa\Model\FileIO;

$init = new \Hakoniwa\Init;

final class FIleIOTest extends TestCase
{
    private $mock;

    public static function setUpBeforeClass(): void
    {
        global $init;
        require_once __DIR__."/../config.php";
        $this->mock = $this->getMockForTrait(FileIO::class);
    }

    /**
     * @dataProvider asset4Rimraf
     */
    public function testRimraf(string $path): void
    {
        $this->assertTrue($this->mock->rimraf($path));
    }

    public function asset4Rimraf()
    {
        global $init;

        $tmpdir = realpath(sys_get_temp_dir()."/test/");
        $pwd    = realpath(__DIR__."/../{$init->dirName}/test/");
        $b = false;

        if (!is_dir($tmpdir)) {
            $b = mkdir($tmpdir, 0755, true);
        }
        if ($b) {
            file_put_contents($tmpdir."test1.txt", "hogefuga");
            file_put_contents($tmpdir."test2.dat", "hogefuga");
            file_put_contents($tmpdir."test3.ini", "hogefuga");
        }
        if (!is_dir($pwd)) {
            $b = mkdir($pwd, 0755, true);
        }
        if ($b) {
            file_put_contents($pwd."test1.txt", "hogefuga");
            file_put_contents($pwd."test2.dat", "hogefuga");
            file_put_contents($pwd."test3.ini", "hogefuga");
        }

        yield "tmpdir" => $tmpdir;
        yield "save folder" => $pwd;
    }
}
