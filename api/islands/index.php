<?php
declare(strict_types=1);

require __DIR__."/../../config.php";

$gethost = function () {
    $https  = $_SERVER["HTTPS"] ?? "";
    $scheme = $https === "off" || $https === "" ? "" : "s";
    $host   = $_SERVER["HTTP_HOST"];
    $path   = $_SERVER["REQUEST_URI"];
    return [
        "url"  => "http{$scheme}://{$host}{$path}",
        "host" => "http{$scheme}://{$host}"
    ];
};
$gethost = $gethost();

if ($_SERVER["HTTP_ORIGIN"] ?? "" !== $gethost["host"]) {
    // exit("hoge");
}


dump_r($_GET);
dump_r($_SERVER);
dump_r($gethost);

$requiredID = parse_url($gethost["url"], PHP_URL_FRAGMENT);

