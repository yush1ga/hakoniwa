<?php

declare(strict_types=1);

namespace Hakoniwa\Model;

require_once __DIR__."/../../config.php";

trait FileIO
{
    public static function read_gameboard_file()
    {
    }

    public static function read_players_data()
    {
    }

    public static function read_alliances_file()
    {
    }

    public static function read_present_file()
    {
    }

    public static function pick_player_data()
    {
    }

    public static function pick_alliance_data()
    {
    }

    public static function write_gameboard_file()
    {
    }

    public static function write_players_file()
    {
    }

    public static function write_alliances_file()
    {
    }

    public static function rimraf(string $path)
    {
        if (is_dir($path)) {
            return false;
        }
        $ls = array_diff(scandir($path), [".", ".."]);
        foreach ($ls as $file) {
            is_dir($path.DS.$file) ? self::rimraf($path.DS.$file) : unlink($path.DS.$file);
        }

        return rmdir($path);
    }

    public static function backup()
    {
    }
}
