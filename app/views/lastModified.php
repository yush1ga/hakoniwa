<div class="lastModified">
<p>
最終更新時間： <?= date("Y年n月j日G時", $hako->islandLastTime) ?><br>
<?= remainTime($hako->islandLastTime + $init->unitTime) ?>
</p>
</div>
<?php
    /* （次のターンまで、残りおよそx日とy時間z分） */
    /* ページを更新してください */
    function remainTime($nextTime) {
        $remainSec = $nextTime - $_SERVER['REQUEST_TIME'];
        $echoVal = '';

        $echoVal .= '<small>（次のターンまで、残りおよそ';

        $echoVal .= ($remainSec/86400 >= 1)? (floor($remainSec/86400).'日と'): '';
        $remainSec %= 86400;

        $echoVal .= ($remainSec/3600 >= 1)? (floor($remainSec/3600).'時間'): '';
        $remainSec %= 3600;

        $echoVal .= ceil($remainSec/60) .'分）</small>';


        if ($remainSec <= 0) {
            $echoVal = '<span style="color:#c00;">ページを更新してください</span>';
        }
        return $echoVal;
    }
