<?php

declare(strict_types=1);

namespace Rekoniwa;

require_once __DIR__."/../config.php";

/**
 * Island data container
 * @copyright 2018 sotalbireo, CGI Game Preservations.
 */
final class Island
{
    /**
     * 島名
     * @var string
     */
    private $name;
    private $owner_name;
    private $num_monster;
    private $num_port;
    private $ships;
    private $id;
    private $start_turn;
    private $is_battlefield;
    private $is_keep;
    private $prizes;
    private $absent;
    private $comment;
    private $comment_date_turn;
    private $password;
    private $point;
    private $point_priv;
    private $satelites;
    private $zins;
    private $items;
    private $money;
    private $money_priv;
    private $num_lottery;
    private $food;
    private $food_priv;
    private $population;
    private $population_priv;
    private $area;
    private $population_of;
    private $num_defeat_monster;
    private $millitary_force_lv;
    private $num_launchable_missile;
    private $weather;
    private $soccer;



    public function __constract($ordinal)
    {
        // $init = new \Hakoniwa\Init;

        $this->getIslandDataFromLegacyDB($ordinal);
    }

    public function getIslandDataFromLegacyDB(int $ordinal)
    {
        $init = new \Hakoniwa\Init;
        $hakojimadat_offset = 4;
        $hakojimadat_eachsize = 18;
        $_base = $hakojimadat_offset + ($hakojimadat_eachsize * $ordinal);

        function csv2mixedArr(string $csv, mixed $pad = ""): array
        {
            return array_pad(explode(",", $csv), mb_substr_count($csv, ",") + 1, $pad);
        }
        function csv2intArr(string $csv, mixed $pad = 0): array
        {
            return array_map("intval", array_pad($csv, mb_substr_count($csv, ",") + 1, $pad));
        }

        $hakojimadat = file(ROOT.DS.$init->dirName.DS."hakojima.dat", FILE_IGNORE_NEW_LINES);

        [
            $this->name, $this->owner_name,
            $this->num_monster, $this->num_port,
            $this->ships[0], $this->ships[1],
            $this->ships[2], $this->ships[3],
            $this->ships[4], $this->ships[5],
            $this->ships[6], $this->ships[7],
            $this->ships[8], $this->ships[9],
            $this->ships[10], $this->ships[11],
            $this->ships[12], $this->ships[13],
            $this->ships[14]
        ] = csv2mixedArr($hakojimadat[$_base]);
        [
            $this->id, $this->start_turn,
            $this->is_battlefield, $this->is_keep
        ] = csv2intArr($hakojimadat[$_base + 1]);
        $this->prizes = trim($hakojimadat[$_base + 2]);
        $this->absent = trim($hakojimadat[$_base + 3]);
        [
            $this->comment, $this->comment_date_turn
        ] = csv2mixedArr($hakojimadat[$_base + 4]);
        $this->password = trim($hakojimadat[$_base + 5]);
        [
            $this->point,
            $this->point_priv
        ] = csv2intArr($hakojimadat[$_base + 6]);
        $this->satelites = csv2intArr($hakojimadat[$_base + 7]);
        $this->zins = csv2intArr($hakojimadat[$_base + 8]);
        $this->items = csv2mixedArr($hakojimadat[$_base + 9], null);
        [
            $this->money,
            $this->num_lottery,
            $this->money_priv
        ] = csv2intArr($hakojimadat[$_base + 10]);
        [
            $this->food,
            $this->food_priv
        ] = csv2intArr($hakojimadat[$_base + 11]);
        [
            $this->population,
            $this->population_priv
        ] = csv2intArr($hakojimadat[$_base + 12]);
        $this->area = trim($hakojimadat[$_base + 13]);
        [
            $this->population_of["agric"],
            $this->population_of["industry"],
            $this->population_of["commerce"],
            $this->population_of["mining"],
            $this->population_of["elec"]
        ] = csv2intArr($hakojimadat[$_base + 14]);
        [
            $this->num_defeat_monster,
            $this->millitary_force_lv,
            $this->num_launchable_missile
        ] = csv2intArr($hakojimadat[$_base + 15]);
        $this->weather = trim($hakojimadat[$_base + 16]);
        [
            $this->soccer["point"],
            $this->soccer["match"],
            $this->soccer["won"],
            $this->soccer["lose"],
            $this->soccer["draw"],
            $this->soccer["atk"],
            $this->soccer["def"],
            $this->soccer["score"],
            $this->soccer["got"],
            $this->soccer["stole"]
        ] = csv2intArr($hakojimadat[$_base + 17]);
    }
}
