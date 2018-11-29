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
    <form name="DataImporter" action="<?=$init->baseDir?>/api/upload.php" method="post" enctype="multipart/form-data" class="ui form">
        <div class="field">
            <label for="ImportZip">ファイルを選ぶ（利用可能なファイルの最大サイズ： <?=$max_size["h"]["file"]?>iB）</label>
            <input id="ImportZip" name="ImportZip" type="file" accept="application/zip">
        </div>
        <div class="field">
            <button id="PostZip" name="PostZip" class="ui fluid button" disabled>バックアップデータを取り込む</button>
        </div>
    </form>
    <button onclick="$('#Check').modal('show');return !1;">show</button>
</div>

</div>
<script>
const $max_size = <?=json_encode($max_size)?>;
<?php require "Script/dataImporter.js";?>
</script>
<div id="Check" class="ui small modal">
    <i class="close icon"></i>
    <div class="header">確認</div>
    <div class="content">
        <div class="ui header">以下のデータをサーバーに展開しますか？</div>
        <p class="ui block header">hogefuga</p>
        <table class="ui compact definition table">
            <tbody>
                <tr>
                    <td class="five wide">ターン数</td>
                    <td class="eleven wide">ｄｆｇｄ</td>
                </tr>
                <tr>
                    <td>更新時刻<br>（ターン処理時刻）</td>
                    <td>dadfhadfhad</td>
                </tr>
                <tr>
                    <td>バックアップ<br>ファイルDL日時</td>
                    <td>dfhadfh</td>
                </tr>
                <tr>
                    <td>レストア先</td>
                    <td>adfhdhafdda</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="actions">
        <button class="ui grey deny button">やっぱやめる</button>
        <button class="ui positive right labeled icon button" disabled>
            展開する <i class="checkmark icon"></i>
        </button>
    </div>
</div>
</section>


<?php
$htmlCore->foot();
