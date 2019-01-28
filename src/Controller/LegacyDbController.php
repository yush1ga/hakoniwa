<?php

declare(strict_types=1);

namespace Hakoniwa\Controller;

require_once ROOT."/../../config.php";

final class LegacyDatasetController
{
    protected function fetchPlayersData(string $filePath): Players
    {
        $filePath = realpath($filePath);
        if ('' === $filePath) {
            throw new \Exception();
        }
        $tmp = file($filePath);
        if (false === $tmp) {
            throw new \Exception();
        }

        return parsePlayersData($tmp);
    }
    private function parsePlayersData(string $rawData): Players
    {}

    // これModel
    protected function pickPlayerData(Players $players, $id): Player
    {
    }



    protected function read_alliances_file(): void
    {
    }

    protected function read_present_file(): void
    {
    }


    protected function write_gameboard_file(): void
    {
    }

    final protected function write_players_file(): void
    {
    }

    final protected function write_alliances_file(): void
    {
    }

}
