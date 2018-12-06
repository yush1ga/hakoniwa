<?php declare(strict_types=1);

// [NOTE] 開発中だと"Router.php"が頭にくるので不発
$fail_1 = get_included_files()[0] === __FILE__;
$fail_2 = $_SERVER["REQUEST_METHOD"] !== "POST";
if ($fail_1 || $fail_2) {
    header("HTTP/1.0 500 Internal Server Error", true, 500);
    exit;
}

$media_type = explode(";", trim(strtolower($_SERVER["CONTENT_TYPE"])))[0];
$INPUT = ($media_type === "application/json")
    ? json_decode(file_get_contents("php://input"), true)
    : $_POST;
unset($fail_1, $fail_2, $media_type);
