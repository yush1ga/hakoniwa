<?php

// [NOTE] 開発中だと"Router.php"が頭にくるので不発
if (get_included_files()[0] === __FILE__) {
    header("HTTP/1.0 500 Internal Server Error", true, 500);
    exit;
}

require __DIR__."/../../config.php";

console_dir($_POST);

header("Content-Type:application/json;charset=utf-8");
echo json_encode($data ?? []);
exit;
