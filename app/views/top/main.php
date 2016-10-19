<h1><?= $init->title ?> <small class="text-muted">トップ</small></h1>
<h2 class='Turn'>ターン<?= $hako->islandTurn ?></h2>

<?php if(($hako->islandTurn % $init->turnPrizeUnit) >= ($init->turnPrizeUnit - 5)): ?>
<div class="alert alert-warning" role="alert">
<h2 class="text-primary"><?= ceil($hako->islandTurn / $init->turnPrizeUnit) * $init->turnPrizeUnit ?>ターン賞争奪戦中！</h2>
</div>
<?php endif ?>

<?php
    if(DEBUG){
        echo '<div class="m-b-2">';
        require_once(VIEWS . '/debug.php');
        echo '</div>';
    }

    $this->lastModified($hako); // 最終更新時刻 ＋ 次ターン更新時刻出力
?>
<hr>

<div class="row">
<div class="col-sm-4">
<?php
    if (count($hako->islandList) != 0) {
        require_once(VIEWS . '/top/my_island.php');
    }
?>
</div>

<div class="col-sm-offset-1 col-sm-7">
<?php $this->infoPrint(); // 「お知らせ」を表示 ?>
</div>
</div><!-- /.row -->

<hr>
