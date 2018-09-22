<?php

declare(strict_types=1);

namespace Hakoniwa\Model;

require_once __DIR__."/../../config.php";

trait FileIO
{
    final protected function read_gameboard_file()
    {
    }

    final protected function read_players_data()
    {
    }

    final protected function read_alliances_file()
    {
    }

    final protected function read_present_file()
    {
    }

    final protected function pick_player_data()
    {
    }

    final protected function pick_alliance_data()
    {
    }

    final protected function write_gameboard_file()
    {
    }

    final protected function write_players_file()
    {
    }

    final protected function write_alliances_file()
    {
    }

    final private function mkfile (string $filepath): bool
    {
        $info = pathinfo($filepath);
        $info["dirname"] = realpath((preg_match("/^([a-zA-Z]:[\/\\]|\/)/", $info["dirname"]) ? "" : __DIR__).DS.$info["dirname"]);

        $filepath = $info["dirname"].DS.$info["filename"];
        $file_stat = $this->is_usable_path($filepath, true)["file"];
        $dir_stat = $this->is_usable_path($info["dirname"], true);

        if ($file_stat || $dir_stat["file"]) {
            return false;
        }

        if (!$dir_stat["dir"]) {
            if(!mkdir($info["dirname"], 0755, true)) {
                return false;
            }
        }

        return file_put_contents($filepath, "", LOCK_EX) !== false;
    }

    final private function parse_path(string $path): string
    {
        $segments = preg_split("/(\/|\\\\)/", $path);
        $parsed_path = [];

        $is_absolute_path = $segments[0] === "" || 1 === preg_match("/[a-zA-Z]:/", $segments[0]);
        $is_windows_os = defined("PHP_WINDOWS_VERSION_MAJOR");
        // $is_windows_path = ;

        if (!$is_absolute_path) {
            $i = 1;
            $depth = 1;
            $parsed_path[0] = __DIR__;
        } else {
            $i = 0;
            $depth = 0;
        }

        for (; $i < count($segments); $i++) {
            $seg = $segments[$i];
            switch($seg) {
                case ".":

                break;
                case "..":
                    $depth -= ($depth === 0) ? 0 : 1;

                break;
                case "":
                    // noop
                break;
                default:
                    $parsed_path[$depth] = $seg;
                    $depth += 1;
                break;
            }
        }

        if ($depth !== count($parsed_path)) {
            array_splice($parsed_path, $depth);
        }

        $is_windows = false;
        $s = DIRECTORY_SEPARATOR;
        return ($is_windows ? "" : $s).implode($s, $parsed_path);
    }

    final private function is_usable_path(string $path, bool $verbose = false): array
    {
        $stat = [
            "file" => false,
            "dir"  => false
        ];
        $stat_verbose = [
            "r" => false,
            "w" => false,
            "x" => false
        ];

        if (!file_exists($path)) {
            return !$verbose ? $stat : array_merge($stat, $stat_verbose);
        }

        $stat["file"] = is_file($path);
        $stat["dir"] = is_dir($path);
        $stat_verbose["r"] = is_readable($path);
        $stat_verbose["w"] = is_writable($path);
        $stat_verbose["x"] = is_executable($path);

        if (!$verbose) {
            $stat["file"] = $stat["file"] && $stat_verbose["r"] && $stat_verbose["w"];
            $stat["dir"] = $stat["dir"] && $stat_verbose["r"] && $stat_verbose["w"];

            return $stat;
        } else {
            return array_merge($stat, $stat_verbose);
        }
    }

    final private function rimraf(string $path): bool
    {
        if (is_dir($path)) {
            return false;
        }
        $ls = array_diff(scandir($path), [".", ".."]);
        foreach ($ls as $file) {
            $p = $path.DS.$file;
            is_dir($p) ? $this->rimraf($p) : unlink($p);
            unset($p);
        }
        unset($ls);

        return rmdir($path);
    }

    final protected function backup()
    {
    }
}
