<section id="RecentlyLog">
<h1>最近の出来事</h1>
<?php
$log = new Log();
for ($i = 0; $i < $init->logTopTurn; $i++) {
    $log->logFilePrint($i);
}
?>
</section>
