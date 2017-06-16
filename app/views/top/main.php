<h1 class="title"><?= $init->title ?> <small class="text-muted">トップ</small></h1>
<h2 class="Turn">ターン<?= $hako->islandTurn ?></h2>

<?php if(($hako->islandTurn % $init->turnPrizeUnit) >= ($init->turnPrizeUnit - 5)): ?>
<div class="alert alert-info" role="alert">
	<h2 class="text-primary">めざせ<?= ceil($hako->islandTurn / $init->turnPrizeUnit) * $init->turnPrizeUnit ?>ターン賞！</h2>
</div>
<?php endif ?>

<?php if(DEBUG): ?>
<div class="m-b-2">
	<?php require_once(VIEWS . '/debug.php'); ?>
</div>
<?php endif; ?>

<?php $this->lastModified($hako); // 最終更新時刻 ＋ 次ターン更新時刻出力 ?>
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
<?php $this->infoPrint(); // 「お知らせ」 ?>
	</div>
</div>

<hr>
