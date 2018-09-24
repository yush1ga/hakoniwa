<?php

declare(strict_types=1);
// error_reporting(E_ALL);
// setlocale(LC_ALL, "ja_JP.UTF-8");

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
    public function testParsePath(string $expected, string $var_path): void
    {
        $method = self::$class->getMethod("parse_path");
        $method->setAccessible(true);
        $this->assertSame($expected, $method->invoke(self::$mock, $var_path));
    }
    public function asset4ParsePath()
    {
        yield __DIR__ => [__DIR__, __DIR__];

        if ($this->run_on_windows()) {
            $driveletter = mb_substr(getcwd(), 0, 1);
            yield "C:\\foo\\bar\\baz" => ["C:\\foo\\bar\\baz", "C:\\foo\\bar\\baz"];
            yield "C:\\foo\\bar\\\\baz" => ["C:\\foo\\bar\\baz", "C:\\foo\\bar\\\\baz"];
            yield "\\foo\\bar\\baz" => [$driveletter.":\\foo\\bar\\baz", "\\foo\\bar\\baz"];
            yield "/foo/bar/baz" => [$driveletter.":\\foo\\bar\\baz", "/foo/bar/baz"];
            yield "C:\\foo\\xxxx\\..\\fuga" => ["C:\\foo\\fuga", "C:\\foo\\xxxx\\..\\fuga"];
            yield "C:\\xxxx\\..\\..\\foo" => ["C:\\foo", "C:\\xxxx\\..\\..\\foo"];
            yield "C:\\qwerty\\.\\asdfgh" => ["C:\\qwerty\\asdfgh", "C:\\qwerty\\.\\asdfgh"];
            yield "C:\\foo\\ＢＡＲ" => ["C:\\foo\\ＢＡＲ", "C:\\foo\\ＢＡＲ"];
            yield "C:\\cn_simplified\\春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext" => ["C:\\cn_simplified\\春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext", "C:\\cn_simplified\\春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext"];
            yield "C:\\cn_tradition\\春眠不覺曉\\處處聞啼鳥\\夜來風雨聲.花落知多少" => ["C:\\cn_tradition\\春眠不覺曉\\處處聞啼鳥\\夜來風雨聲.花落知多少", "C:\\cn_tradition\\春眠不覺曉\\處處聞啼鳥\\夜來風雨聲.花落知多少"];
            yield "C:/foo/with space/fuga.ext" => ["C:\\foo\\with space\\fuga.ext", "C:/foo/with space/fuga.ext"];
            yield "./foo/fuga.ext" => [getcwd()."\\foo\\fuga.ext", "./foo/fuga.ext"];
            yield "./../foo.ext" => [mb_substr(getcwd(), 0, mb_strrpos(getcwd(), "\\"))."\\foo.ext", "./../foo.ext"];
        } else {
            yield "/foo/bar/baz" => ["/foo/bar/baz", "/foo/bar/baz"];
            yield "/foo/bar//baz" => ["/foo/bar/baz", "/foo/bar//baz"];
            yield "/foo/xxxx/../fuga" => ["/foo/fuga", "/foo/xxxx/../fuga"];
            yield "/xxxx/../../foo" => ["/foo", "/xxxx/../../foo"];
            yield "/foo/ＦＵＧＡ" => ["/foo/ＦＵＧＡ", "/foo/ＦＵＧＡ"];
            yield "/cn_simplified/春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext" => ["/cn_simplified/春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext", "/cn_simplified/春眠不觉晓处处闻啼鸟夜来风雨声花落知多少.ext"];
            yield "/cn_tradition/春眠不覺曉/處處聞啼鳥/夜來風雨聲.花落知多少" => ["/cn_tradition/春眠不覺曉/處處聞啼鳥/夜來風雨聲.花落知多少", "/cn_tradition/春眠不覺曉/處處聞啼鳥/夜來風雨聲.花落知多少"];
            yield "/foo/with space/fuga.ext" => ["/foo/with space/fuga.ext", "/foo/with space/fuga.ext"];
            yield "c://foo/bar" => ["c:/foo/bar", "c:\\\\foo\\\\bar"];
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
            throw new \Exception("Failed `mkdir` to ".$path);
        }

        $path = $this->parse_path(getcwd()."/{$init->dirName}".$test_dir);
        if (mkdir($path, 0777, true)) {
            yield "save_dir #1" => [$path."/test1.txt"];
            yield "save_dir #2" => [$path."/test2.dat"];
            yield "save_dir #3" => [$path."/test3/test3.log"];
        } else {
            throw new \Exception("Failed `mkdir` to ".$path);
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



    final private function run_on_windows(): bool
    {
        return defined("PHP_WINDOWS_VERSION_MAJOR");
    }



    final private function parse_path(string $path): string
    {
        $mock = (new class extends TestCase {
        })->getMockForTrait(FileIO::class);
        $class = new \ReflectionClass($mock);
        $fn = $class->getMethod("parse_path");
        $fn->setAccessible(true);

        return $fn->invoke($mock, $path);
    }
}
