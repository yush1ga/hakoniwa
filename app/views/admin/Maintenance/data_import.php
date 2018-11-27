<?php declare(strict_types=1);

require_once __DIR__."/../../../../config.php";
$init = new \Hakoniwa\Init;

require_once ROOT."/beta/ssi/_core.php";
$htmlCore = new \HtmlCore;
$htmlCore->head("test")->gnav();

$max_size = [
    "h" => [
        "amount" => ini_get("post_max_size"),
        "file"   => ini_get("upload_max_filesize")
    ],
    "r" => [
        "amount" => $htmlCore->iniVal2rawVal(ini_get("post_max_size")),
        "file"   => $htmlCore->iniVal2rawVal(ini_get("upload_max_filesize"))
    ]
];
?>



<div class="ui container">
<?=$htmlCore->h1("Management tools [BETA]", "- Data importer");?>
</div>

<div class="ui container">
<div class="ui raised segment">
    <h2>data select...</h2>
    <form name="DataImporter" action="" method="post" enctype="" class="ui form">
        <div class="field">
            <label for="ImportZip">ファイルを選ぶ</label>
            <input id="ImportZip" name="ImportZip" type="file" accept="application/zip">
        </div>
        <div class="field">
            <button class="ui fluid button" disabled>バックアップデータを取り込む</button>
        </div>
    </form>
</div>
</div>
<script>
const $max_size = <?=json_encode($max_size)?>;
<?php require "Script/dataImporter.js";?>
</script>



<?php
$htmlCore->foot();
