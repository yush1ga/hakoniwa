<?php

declare(strict_types=1);

namespace Hakoniwa\Model;

if (!defined("WINDOWS")) {
    throw new \ErrorException("Not defined: `WINDOWS`.");
}

trait FileIO
{
    // abstract private $file_path;

    final protected function read_gameboard_file(): bool
    {
        // if (!isset($file_path["gameboard"] || realpath())
        // {}
        return true;
    }

    final protected function read_players_data(): void
    {
    }

    final protected function read_alliances_file(): void
    {
    }

    final protected function read_present_file(): void
    {
    }

    final protected function pick_player_data(): void
    {
    }

    final protected function pick_alliance_data(): void
    {
    }

    final protected function write_gameboard_file(): void
    {
    }

    final protected function write_players_file(): void
    {
    }

    final protected function write_alliances_file(): void
    {
    }

    final private function mkfile(string $filepath, string $content = ""): bool
    {
        $info = pathinfo($this->parse_path($filepath));

        $filepath = $info["dirname"].DIRECTORY_SEPARATOR.$info["basename"];
        $file_stat = $this->is_usable_path($filepath, true)["file"];
        $dir_stat = $this->is_usable_path($info["dirname"], true);

        if ($file_stat || $dir_stat["file"]) {
            return false;
        }

        if (!$dir_stat["dir"]) {
            if (!mkdir($info["dirname"], 0775, true)) {
                return false;
            }
        }

        return file_put_contents($filepath, $content, LOCK_EX) !== false;
    }



    final private function parse_path(string $path): string
    {
        $segments = preg_split("/(\/|\\\\)/", $path);
        $parsed_path = [];
        $s = DIRECTORY_SEPARATOR;
        $cwd = getcwd();

        $has_driveletter = 1 === preg_match("/^[a-zA-Z]:\.?$/", $segments[0]);
        $is_absolute_path = $segments[0] === "" || $has_driveletter;

        if (!WINDOWS && $has_driveletter) {
            throw new \InvalidArgumentException("Failed parse: `{$path}`");
        }

        if ($segments[0] === "~") {
            $home = WINDOWS ? getenv("USERPROFILE") : (getenv("PATH") ?? posix_getpwuid(posix_geteuid())["dir"]);

            if (false !== $home) {
                return $this->parse_path($home.mb_substr($path, 1));
            } else {
                throw new \RuntimeException("Failed parse: `{$path}`");
            }
        }

        if (!$is_absolute_path) {
            return $this->parse_path($cwd.$s.$path);
        }

        $depth = 0;
        foreach ($segments as $seg) {
            if ($depth !== 0 && 1 === preg_match("/^[a-zA-Z]:\.?$/", $seg)) {
                throw new \RuntimeException("Failed parse: `{$path}`");
            }
            switch ($seg) {
                case ".":
                case "":
                    // noop
                break;
                case "..":
                    $depth -= ($depth === 0) ? 0 : 1;
                    $depth = (WINDOWS && $has_driveletter) ? max($depth, 1) : $depth;

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


        if (WINDOWS) {
            if ($has_driveletter) {
                $path_prefix = "";
                $parsed_path[0] = mb_strtoupper($parsed_path[0]);
            } else {
                $path_prefix = mb_substr($cwd, 0, mb_strpos($cwd, ":") + 1).$s;
            }
        } else {
            $path_prefix = $s;
        }

        return $path_prefix.implode($s, $parsed_path);
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



    final protected function rimraf(string $path): bool
    {
        if (is_file($this->parse_path($path))) {
            return unlink(realpath($this->parse_path($path)));
        }

        $ls = array_diff(scandir($path), [".", ".."]);
        foreach ($ls as $file) {
            $p = realpath($path.DIRECTORY_SEPARATOR.$file);
            chmod($p, 0777);
            is_dir($p) ? $this->rimraf($p) : unlink($p);
        }
        unset($p, $ls);

        return rmdir($path);
    }



    final protected function cp_a(string $from, string $to, bool $recursion = false): void
    {
        $from = $this->parse_path($from);
        $to = $this->parse_path($to);

        if (!$recursion) {
            if (!is_dir($from)) {
                throw new \InvalidArgumentException("Arguments must directory: `{$from}`.");
            }
            if (!$this->is_usable_path($from)["dir"]) {
                throw new \ErrorException("No have permission to Read/Write: `{$from}`.");
            }
            if (!is_dir($to)) {
                if (!mkdir($to, 0775, true)) {
                    throw new \ErrorException("No have permission to Read/Write: `{$to}`.");
                }
            } else {
                $t[0] = $this->is_usable_path($to)["dir"];
                $t[1] = $this->filelist($to);
                if (!$t[0]) {
                    throw new \ErrorException("Already exists, but not be allow read/write: `{$to}`.");
                }
                if ($t[1] !== []) {
                    throw new \ErrorException("Already exists and not empty directory: `{$to}`.");
                }
            }
        }

        $ls = array_diff(scandir($from), [".", "..", ".git", "vendor", "node_modules"]);
        foreach ($ls as $file) {
            $f = $from.DIRECTORY_SEPARATOR.$file;
            $t = $to.DIRECTORY_SEPARATOR.$file;
            if (is_dir($f)) {
                mkdir($t);
                $this->cp_a($f, $t, true);
            } else {
                copy($f, $t);
            }
        }
        unset($f, $t, $ls);

        return;
    }



    final private function filelist(string $dir, array $exclude_prefix = []): array
    {
        $rii =  new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \FilesystemIterator::SKIP_DOTS
                | \FilesystemIterator::KEY_AS_PATHNAME
                | \FilesystemIterator::CURRENT_AS_PATHNAME
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $dir = realpath($dir);

        $filelist = [];
        foreach ($rii as $key => $value) {
            $rel = mb_substr(realpath($key), mb_strlen($dir));
            if (!$this->starts_with($rel, $exclude_prefix)) {
                $filelist[$rel] = $value;
            }
        }

        return $filelist;
    }



    final protected function is_same(string $orig, string $targ, array $exclude = []): bool
    {
        $exclude = array_unique(array_merge($exclude, [".git", "vendor", "node_modules"]));

        if (is_dir($orig) && is_dir($targ)) {
            $orig_files = $this->filelist($orig, $exclude);
            $targ_files = $this->filelist($targ, $exclude);

            foreach ($orig_files as $rel => $orig_path) {
                if (array_key_exists($rel, $targ_files)) {
                    if (!hash_equals(hash_file("sha256", $orig_path), hash_file("sha256", $targ_files[$rel]))) {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            return true;
        }

        if (is_file($orig) && is_file($targ)) {
            return hash_equals(hash_file("sha256", $orig), hash_file("sha256", $targ));
        }

        throw new \InvalidArgumentException("You have to choose arguments pair either \"Directory-Directory\" or \"File-File\".");
    }



    final protected function mkdir_tmp(string $suffix = "")
    {
        $suffix = $suffix !== "" ?: $this->random_str();
        $tmp_dir = $this->parse_path(sys_get_temp_dir().DIRECTORY_SEPARATOR.$suffix);
        if (mkdir($tmp_dir, 0777, true)) {
            return $tmp_dir;
        }

        return false;
    }


    // [TODO] move to \Util
    final private function random_str(int $length = 8): string
    {
        static $seeds;

        if (!$seeds) {
            $seeds = array_flip(array_merge(range("a", "z"), range("A", "Z"), range("0", "9")));
        }
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= array_rand($seeds);
        }

        return $str;
    }

    // [TODO] move to \
    final private function starts_with(string $str, $prefix): bool
    {
        $type = gettype($prefix);
        switch ($type) {
            case "string":
                $prefix = $prefix[0] === DIRECTORY_SEPARATOR ?: DIRECTORY_SEPARATOR.$prefix;

                return mb_substr($str, 0, mb_strlen($prefix)) === $prefix;
            case "array":
                foreach ($prefix as $p) {
                    $p = $p[0] === DIRECTORY_SEPARATOR ?: DIRECTORY_SEPARATOR.$p;
                    if (mb_substr($str, 0, mb_strlen($p)) === $p) {
                        return true;
                    }
                }

                return false;
        }

        throw new \InvalidArgumentException("Arguments #1 require type of `String[] | String` (Actual `{$type}`)");
    }
}
