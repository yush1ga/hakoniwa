<?php

declare(strict_types=1);

require_once __DIR__."/../config.php";

use \PHPUnit\Framework\TestCase;
use \Hakoniwa\Init;
use \Hakoniwa\InitDefault;

final class InitTest extends TestCase
{
    protected static $refInit;

    public static function setUpBeforeClass(): void
    {
        self::$refInit = new \ReflectionClass(new Init);
    }

    public function testInitHasNotIllegaleProperty(): void
    {
        foreach (self::$refInit->getDefaultProperties() as $key => $value) {
            $this->assertClassHasAttribute($key, InitDefault::class);
        }
    }
}
