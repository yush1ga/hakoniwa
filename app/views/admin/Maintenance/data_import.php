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

</div>

<?php
$htmlCore::foot();
