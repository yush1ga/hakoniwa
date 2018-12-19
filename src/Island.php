<?php

class Island
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
    private $absense;
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
    private $population_of = [
        "agric"    => 1,
        "industry" => 1,
        "commerce" => 1,
        "Mining"   => 1,
        "elec"     => 1,
    ];
    private $num_destroyed_monster;
    private $millitary_force_lv;
    private $num_lunchable_missile;
    private $weather;
    private $soccer = [
        "point" => 1,
        "match" => 1,
        "won"   => 1,
        "lose"  => 1,
        "draw"  => 1,
        "atk"   => 1,
        "def"   => 1,
        "score" => [
            "got"   => 1,
            "stole" => 1,
        ],
    ] | null;
}
