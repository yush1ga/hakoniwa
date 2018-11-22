<?php declare(strict_types=1);

require_once __DIR__."/../../../../config.php";
$init = new \Hakoniwa\Init;

$htmlCore = new HtmlCore;

$htmlCore::head("test");
$htmlCore::gnav();
?>
<div class="ui container">
<?=$htmlCore::h1("Management tools", "- Data importer");?>
</div>

<div class="ui container">
<h2>data select...</h2>
<form action="" class="ui form">
    <div class="field">
        <label for="ImportZip">ファイルを選ぶ</label>
        <input id="ImportZip" type="file" accept=".zip,application/zip">
    </div>
</form>
</div>

<?php
$htmlCore::foot();
