<?php

declare(strict_types=1);

require __DIR__."/../../_/getPostApi.php";
require __DIR__."/../../../config.php";

$init = new \Hakoniwa\Init;

function cp_a(string $from, string $to): bool
{
    return (new class {
        use \Hakoniwa\Model\FileIO {
            cp_a as public;
        }
    })->cp_a($from, $to);
}
function rimraf(string $p): void
{
    (new class {
        use \Hakoniwa\Model\FileIO {
            rimraf as public;
        }
    })->rimraf($p);
}



$f = sys_get_temp_dir().DS.$INPUT["extract"].DS."hakoniwa".DS."data";
$t = dirname(ROOT.$init->dirName).DS.$INPUT["restoreTo"];

$data["done"] = cp_a($f, $t);
rimraf($f);



header("Content-Type:application/json;charset=utf-8");
echo json_encode($data ?? []);
exit;
