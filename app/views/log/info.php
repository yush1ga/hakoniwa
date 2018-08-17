<div id="HistoryLog">
<h2>お知らせ <span class="small" style="font-size:.5em;">[編集<span class="sr-only">する</span>]</span></h2>
<div style="overflow-y:scroll;word-break:break-all;height:<?=$init->divHeight?>px;">
<?php
    (new Log())->infoPrint();
?>
</div>
</div>


