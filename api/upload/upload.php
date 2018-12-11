<?php

// [NOTE] 開発中だと"Router.php"が頭にくるので不発
if (get_included_files()[0] === __FILE__) {
    header("HTTP/1.0 500 Internal Server Error", true, 500);
    exit;
}

require __DIR__."/../../config.php";

$file = $_FILES["ImportZip"];
$init = new \Hakoniwa\Init;



function rimraf(string $path): void
{
    (new class {
        use \Hakoniwa\Model\FileIO {
            rimraf as public;
        }
    })->rimraf($path);
}



if (is_uploaded_file($file["tmp_name"])) {
    $tmp_extract_to = sys_get_temp_dir().DS."php_".\Util::random_str(4);
    $tmp_zip_path = $tmp_extract_to.DS.$file["name"];
    mkdir($tmp_extract_to, 0777, true);
    $zipper = new \Hakoniwa\Utility\Zipper($file["name"]);
    $zipper->extractTo($file["tmp_name"], $tmp_extract_to);
    $extract_files = \Util::filelist($tmp_extract_to);



    $ddtf_path = "";
    $hkjm_path = [];
    foreach ($extract_files as $k => $v) {
        if ("__DONT_DELETE_THIS_FILE__" === mb_substr($k, -25)) {
            $ddtf_path = $v;

            break;
        }
    }
    foreach ($extract_files as $k => $v) {
        if ("hakojima.dat" === mb_substr($k, -12)) {
            $hkjm_path[] = $v;
        }
    }
    if ($ddtf_path === "" || count($hkjm_path) !== 1) {
        header("Content-Type:application/json;charset=utf-8");
        echo json_encode([
            "error" => "仕様外のzipファイルです。正しいファイルを再度アップロードしてください。",
            "extract"    => $tmp_extract_to
        ]);
        rimraf($tmp_extract_to);
        exit;
    }


    $g_data = parse_ini_file($ddtf_path);
    $hkjm = file($hkjm_path[0], FILE_IGNORE_NEW_LINES);
    $restoreTo = "data".date("Ymd-HisT", $hkjm[1])."_".\Util::random_str(2);

    // verify & data input
    $data = [
        "gameTitle"  => $g_data["GAME_TITLE"] ?? "",
        "backupDate" => $hkjm[1] ?? -1,
        "backupTurn" => $hkjm[0] ?? -1,
        "zippedDate" => $g_data["REQUEST_TIME"] ?? 0,
        "restoreTo"  => $restoreTo ?? "",
        "extract"    => basename($tmp_extract_to)
    ];
}



header("Content-Type:application/json;charset=utf-8");
echo json_encode($data ?? []);
exit;
