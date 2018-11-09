<?php

declare(strict_types=1);

error_reporting(-1);

use \PHPUnit\Framework\TestCase;
use \Hakoniwa\Helper\Util;

$init = new \Hakoniwa\Init;

final class TurnTest extends TestCase
{
    protected $turn;

    public function setUp(): void
    {
        $this->turn = new \Turn;
    }


    /**
     * @dataProvider dpIncome
     */
    public function testIncome(array $expected, array $actual): void
    {
        // $this->markTestIncomplete();

        $this->turn->income($actual);
        $this->assertEquals($expected, $actual);
    }
    public function dpIncome()
    {
        $d["pop"] = 10000;
        $d["money"] = $d["food"] = 1000;
        $d["farm"] = $d["factory"] = $d["commerce"] = $d["mountain"] = $d["hatuden"] = 100;
        $d["tenki"] = 1;
        $d["id"] = "-999";
        $d["name"] = "test";
        $d["zin"] = array_fill(0, 7, 0);
        $d["port"] = 0;
        $d["ship"] = array_fill(0, 7, 0);

        $exp = $d;

        yield "#1" => [$exp, $d];
    }
}
