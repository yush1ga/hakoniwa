<?php

declare(strict_types=1);

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
        /**
         * Unit:
         *   - pop:     100
         *   - hatuden: 1000
         *   - money:   1
         *   - food:    100
         *   - farm:    1000
         *   - factory: 1000
         *   - commerce:1000
         *   - mountain:1000
         */
        $b["pop"] = 1000;
        $b["money"] = $b["food"] = 1000;
        $b["hatuden"] = 100;
        $b["farm"] = $b["factory"] = $b["commerce"] = $b["mountain"] = 10;
        $b["tenki"] = 1;
        $b["id"] = "-999";
        $b["name"] = "test";
        $b["zin"] = array_fill(0, 7, 0);
        $b["port"] = 0;
        $b["ship"] = array_fill(0, 7, 0);

        $exp = $d = $b;
        $exp["money"] = 1600.0;
        $exp["food"]  = 900.0;
        yield "#1" => [$exp, $d];

        $exp = $d = $b;
        $exp["hatuden"] = $d["hatuden"] = 0;
        $exp["farm"] = $d["farm"] = 0;
        $exp["money"] = 1000.0;
        $exp["food"] = 800.0;
        yield "#2" => [$exp, $d];

        $exp = $d = $b;
        $exp["zin"][6] = $d["zin"][6] = 1;
        $exp["zin"][5] = $d["zin"][5] = 1;
        $exp["money"] = 2200.0;
        $exp["food"] = 1000.0;
        yield "#3" => [$exp, $d];

        $exp = $d = $b;
        $exp["hatuden"] = $d["hatuden"] = 15;
        $exp["money"] = 1300.0;
        $exp["food"]  = 900.0;
        yield "#4" => [$exp, $d];

        $exp = $d = $b;
        $exp["money"] = $d["money"] = 0;
        $exp["food"] = $d["food"] = 0;
        $exp["farm"] = $d["farm"] = $exp["factory"] = $d["factory"] = 0;
        $exp["commerce"] = $d["commerce"] = $exp["mountain"] = $d["mountain"] = 0;
        $exp["ship"][3] = $d["ship"][3] = 2;
        $exp["money"] = 0.0;
        $exp["food"]  = 0.0;
        yield "#5" => [$exp, $d];
    }
}
