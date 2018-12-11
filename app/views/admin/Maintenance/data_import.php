<?php declare(strict_types=1);

require_once __DIR__."/../../../../config.php";
$init = new \Hakoniwa\Init;

require_once ROOT."/beta/ssi/_core.php";
$htmlCore = new \HtmlCore;

$max_size = [
    "h" => [
        "amount" => trim(ini_get("post_max_size")),
        "file"   => trim(ini_get("upload_max_filesize"))
    ],
    "r" => [
        "amount" => $htmlCore->iniVal2rawVal(ini_get("post_max_size")),
        "file"   => $htmlCore->iniVal2rawVal(ini_get("upload_max_filesize"))
    ]
];




$htmlCore->head("test")->gnav();
?>

<section>
<div class="ui container">
<?=$htmlCore->h1("Management tools [BETA]", "- Data importer");?>
</div>

<div class="ui container">
<div class="ui raised segment">
    <h2>data select...</h2>
    <form name="DataImporter" action="<?=$init->baseDir?>/api/upload/upload.php" method="post" enctype="multipart/form-data" class="ui form">
        <div class="field">
            <label for="ImportZip">ファイルを選ぶ（利用可能なファイルの最大サイズ： <?=$max_size["h"]["file"]?>iB）</label>
            <input id="ImportZip" name="ImportZip" type="file" accept="application/zip">
        </div>
        <div class="field">
            <button id="PostZip" name="PostZip" class="ui fluid button" disabled>バックアップデータを取り込む</button>
        </div>
    </form>
</div>

</div>
<div id="jsCheck" class="ui small modal">
    <div class="header">確認</div>
    <div class="content">
        <div class="ui header">以下のデータをサーバーに展開しますか？</div>
        <p id="jsGameTitle" class="ui block center aligned header">--------</p>
        <table class="ui compact definition table">
            <tbody>
                <tr>
                    <td class="five wide">ターン数</td>
                    <td id="jsBackupTurn" class="eleven wide">----</td>
                </tr>
                <tr>
                    <td>更新時刻<br>（ターン処理時刻）</td>
                    <td id="jsBackupDate">--------</td>
                </tr>
                <tr>
                    <td>バックアップ<br>ファイルDL日時</td>
                    <td id="jsZippedDate">--------</td>
                </tr>
                <tr>
                    <td>展開フォルダ名</td>
                    <td id="jsRestoreTo">--------</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="content" style="display:none">
        <p></p>
    </div>
    <div class="actions">
        <button class="ui grey cancel left labeled icon button" disabled>
            <i class="close icon"></i> やっぱやめる
        </button>
        <button class="ui primary ok right labeled icon button" disabled>
            展開する <i class="checkmark icon"></i>
        </button>
    </div>
</div>
<script>
const $max_size = <?=json_encode($max_size)?>;
const $baseDir = "<?=$init->baseDir?>";
<?php require "Script/dataImporter.js";?>
</script>
</section>


<?php
$htmlCore->foot();
