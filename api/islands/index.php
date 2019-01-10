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
    // header("HTTP/1.0 500 Internal Server Error", true, 500);
    // exit;
}



// $requiredID = parse_url($gethost["url"], PHP_URL_FRAGMENT);
parse_str(parse_url($gethost["url"], PHP_URL_QUERY) ?? "", $query);



// (int)$query["r"]

$isl = new \Rekoniwa\Island((int)$query["r"]);

dump($isl->getPublicDataJson());
// dump($isl);
// dump($isl->getIslandDataFromLegacyDB());
