<?php

declare(strict_types=1);

require_once __DIR__."/../config.php";

use \PHPUnit\Framework\TestCase;
use \Hakoniwa\Model\FileIO;

$init = new \Hakoniwa\Init;
$test_dir = "/test".time();

final class FileIOTest extends TestCase
{
    protected static $mock;
    protected static $class;

    public static function setUpBeforeClass(): void
    {
        clearstatcache();
        self::$mock = (new class extends TestCase {
        })->getMockForTrait(FileIO::class);
        self::$class = new \ReflectionClass(self::$mock);
    }



    /**
     * @dataProvider asset4ParsePath
     */
    public function testParsePathForSuccess(string $expected, string $var_path): void
    {
        $method = self::$class->getMethod("parse_path");
        $method->setAccessible(true);
        $this->assertSame($expected, $method->invoke(self::$mock, $var_path));
    }
    public function asset4ParsePath()
    {
        yield __DIR__ => [__DIR__, __DIR__];

        if (WINDOWS) {
            $driveletter = mb_substr(getcwd(), 0, 1);
            yield "for Windows #1" => ["C:\\foo\\bar\\baz", "C:\\foo\\bar\\baz"];
            yield "for Windows #2" => ["C:\\foo\\bar\\baz", "C:\\foo\\bar\\\\baz"];
            yield "for Windows #3" => [$driveletter.":\\foo\\bar\\baz", "\\foo\\bar\\baz"];
            yield "for Windows #4" => [$driveletter.":\\foo\\bar\\baz", "/foo/bar/baz"];
            yield "for Windows #5" => ["C:\\foo\\bar", "C:\\foo\\xxxx\\..\\bar"];
            yield "for Windows #6" => ["C:\\foo", "C:\\xxxx\\..\\..\\foo"];
            yield "for Windows #7" => ["C:\\foo\\bar", "C:\\foo\\.\\bar"];
            yield "for Windows #8" => ["C:\\foo\\ＢＡＲ", "C:\\foo\\ＢＡＲ"];
            yield "for Windows #9" => ["C:\\cn_simplified\\春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext", "C:\\cn_simplified\\春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext"];
            yield "for Windows #10" => ["C:\\cn_tradition\\春眠不覺曉\\處處聞啼鳥\\夜來風雨聲.花落知多少", "C:\\cn_tradition\\春眠不覺曉\\處處聞啼鳥\\夜來風雨聲.花落知多少"];
            yield "for Windows #11" => ["C:\\foo\\with space\\bar.ext", "C:/foo/with space/bar.ext"];
            yield "for Windows #12" => [getcwd()."\\foo\\bar.ext", "./foo/bar.ext"];
            yield "for Windows #13" => [mb_substr(getcwd(), 0, mb_strrpos(getcwd(), "\\"))."\\foo.ext", "./../foo.ext"];
            yield "for Windows #14" => [getenv("USERPROFILE", true)."\\foo.ext", "~/foo.ext"];
        } else {
            yield "for Linux... #1" => ["/foo/bar/baz", "/foo/bar/baz"];
            yield "for Linux... #2" => ["/foo/bar/baz", "/foo/bar//baz"];
            yield "for Linux... #3" => ["/foo/bar", "/foo/xxxx/../bar"];
            yield "for Linux... #4" => ["/foo", "/xxxx/../../foo"];
            yield "for Linux... #5" => ["/foo/ＦＵＧＡ", "/foo/ＦＵＧＡ"];
            yield "for Linux... #6" => ["/cn_simplified/春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext", "/cn_simplified/春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext"];
            yield "for Linux... #7" => ["/cn_tradition/春眠不覺曉/處處聞啼鳥/夜來風雨聲.花落知多少", "/cn_tradition/春眠不覺曉/處處聞啼鳥/夜來風雨聲.花落知多少"];
            yield "for Linux... #8" => ["/foo/with space/bar.ext", "/foo/with space/bar.ext"];
            yield "for Linux... #9" => [getcwd()."/foo/bar.ext", "./foo/bar.ext"];
            yield "for Linux... #10" => [mb_substr(getcwd(), 0, mb_strrpos(getcwd(), "/"))."/foo.ext", "./../foo.ext"];
            yield "for Linux... #11" => [$_SERVER["HOME"]."/foo.ext", "~/foo.ext"];
        }
    }



    /**
     * @dataProvider asset4ParsePathForThrowError
     */
    public function testParsePathForThrowError(string $path, string $errorClass): void
    {
        $method = self::$class->getMethod("parse_path");
        $method->setAccessible(true);

        $this->expectException($errorClass);
        $method->invoke(self::$mock, $path);
    }
    public function asset4ParsePathForThrowError()
    {
        yield "test #1" => ["/foo/C:/bar", \RuntimeException::class];
        if (!WINDOWS) {
            yield "test #2" => ["C:\\foo", \InvalidArgumentException::class];
        }
    }



    /**
     * @dataProvider asset4Mkfile
     */
    public function testMkfile(string $path): void
    {
        $method = self::$class->getMethod("mkfile");
        $method->setAccessible(true);

        $this->assertTrue($method->invoke(self::$mock, $path));
        $this->assertFileExists($path);
    }
    public function asset4Mkfile()
    {
        global $init, $test_dir;

        $path = $this->parse_path(sys_get_temp_dir().$test_dir);
        if (mkdir($path, 0777, true)) {
            yield "tmpdir #1" => [$path."/test1.txt"];
            yield "tmpdir #2" => [$path."/test2.dat"];
            yield "tmpdir #3" => [$path."/test3/test3.log"];
        } else {
            throw new \RuntimeException("Failed `mkdir` to ".$path);
        }

        $path = $this->parse_path(getcwd()."/{$init->dirName}".$test_dir);
        if (mkdir($path, 0777, true)) {
            yield "save_dir #1" => [$path."/test1.txt"];
            yield "save_dir #2" => [$path."/test2.dat"];
            yield "save_dir #3" => [$path."/test3/test3.log"];
        } else {
            throw new \RuntimeException("Failed `mkdir` to ".$path);
        }
    }



    /**
     * @dataProvider asset4Rimraf
     */
    public function testRimraf(string $path): void
    {
        $fn = self::$class->getMethod("rimraf");
        $fn->setAccessible(true);

        $this->assertTrue($fn->invoke(self::$mock, $path));
    }
    public function asset4Rimraf()
    {
        global $init, $test_dir;

        $path = $this->parse_path(sys_get_temp_dir().$test_dir);
        yield "tmpdir" => [$path];

        $path = $this->parse_path(getcwd()."/{$init->dirName}".$test_dir);
        yield "save_dir" => [$path];
    }



    public function testCp_a(): void
    {
        global $test_dir;

        $from = $this->parse_path(sys_get_temp_dir().$test_dir."from");
        $to = $this->parse_path(sys_get_temp_dir().$test_dir."to");
        $to2 = $this->parse_path(sys_get_temp_dir().$test_dir."日本語フォルダ（long_dir_name）");

        $mkfile = self::$class->getMethod("mkfile");
        $mkfile->setAccessible(true);
        $mkfile->invoke(self::$mock, $from."/test1.txt");
        $mkfile->invoke(self::$mock, $from."/test2.dat");
        $mkfile->invoke(self::$mock, $from."/test3/test3.log");
        $this->assertFileExists($from."/test1.txt");
        $this->assertFileExists($from."/test2.dat");
        $this->assertFileExists($from."/test3/test3.log");
        unset($mkfile);

        $cp_a = self::$class->getMethod("cp_a");
        $cp_a->setAccessible(true);
        $cp_a->invoke(self::$mock, $from, $to);
        $this->assertFileExists($to."/test1.txt");
        $this->assertFileExists($to."/test2.dat");
        $this->assertFileExists($to."/test3/test3.log");
        $cp_a->invoke(self::$mock, $from, $to2);
        $this->assertFileExists($to2."/test1.txt");
        $this->assertFileExists($to2."/test2.dat");
        $this->assertFileExists($to2."/test3/test3.log");
        unset($cp_a);
    }



    public function testIs_same(): void
    {
        global $init, $test_dir;

        $from = $this->parse_path(sys_get_temp_dir().$test_dir."from");
        $to = $this->parse_path(sys_get_temp_dir().$test_dir."to");
        $to2 = $this->parse_path(sys_get_temp_dir().$test_dir."日本語フォルダ（long_dir_name）");

        $method = self::$class->getMethod("is_same");
        $method->setAccessible(true);
        $this->assertTrue($method->invoke(self::$mock, $from, $to));
        $this->assertTrue($method->invoke(self::$mock, $from."/test1.txt", $to."/test1.txt"));
        $this->assertTrue($method->invoke(self::$mock, $from, $to2));
        $this->assertTrue($method->invoke(self::$mock, $from."/test1.txt", $to2."/test1.txt"));

        $rimraf = self::$class->getMethod("rimraf");
        $rimraf->setAccessible(true);
        $rimraf->invoke(self::$mock, $from);
        $rimraf->invoke(self::$mock, $to);
        $rimraf->invoke(self::$mock, $to2);
        unset($rimraf);
    }





    private function parse_path(string $path): string
    {
        $mock = (new class extends TestCase {
        })->getMockForTrait(FileIO::class);
        $class = new \ReflectionClass($mock);
        $fn = $class->getMethod("parse_path");
        $fn->setAccessible(true);

        return $fn->invoke($mock, $path);
    }
}
