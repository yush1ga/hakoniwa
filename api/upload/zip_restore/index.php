<?php declare(strict_types=1);

require __DIR__."/../../_/getPostApi.php";
require __DIR__."/../../../config.php";

$init = new \Hakoniwa\Init;

console_dir($INPUT);

function cp_a (string $from, string $to): bool
{
    return (new class {
        use \Hakoniwa\Model\FileIO;
        public function a($f, $t): void
        {
            return $this->cp_a($f, $t);
        }
    })->a($from, $to);
}



$data["done"] = cp_a($INPUT["dir"], dirname(ROOT."/{$init->baseDir}")."/{$INPUT["restoreTo"]}");


header("Content-Type:application/json;charset=utf-8");
echo json_encode($data ?? []);
exit;
