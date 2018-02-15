<div id="NewIsland">
	<h2>新しい島を探す</h2>

<?php if ($number < $init->maxIsland): ?>

	<?php if ($init->registerMode == 1 && $init->adminMode == 0): ?>
	<div class="alert alet-info">
		当箱庭では不適当な島名などの事前チェックを行っています。<br>
		参加希望の方は、管理者に「島名」と「パスワード」を送信してください。
	</div>

	<?php else: ?>
	<form action="<?= $this_file ?>" method="post">

		<div class="form-group">
			<label>どんな名前をつける予定？</label>
			<div class="input-group">
				<input type="text" class="form-control" name="ISLANDNAME" size="32" maxlength="32" required>
				<span class="input-group-addon"><?= $init->nameSuffix; ?></span>
			</div>
		</div>

		<div class="form-group">
			<label>あなたのお名前は？（省略可）</label>
			<input type="text" class="form-control" name="OWNERNAME" size="32" maxlength="32">
		</div>

		<div class="form-group">
			<label>パスワードは？</label>
			<input type="password" class="form-control" name="PASSWORD" size="32" maxlength="32" required>
		</div>
		<div class="form-group">
			<label>パスワード（確認のためもう一度）</label>
			<input type="password" class="form-control" name="PASSWORD2" size="32" maxlength="32" required>
		</div>

		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="探しに行く">
		</div>

		<input type="hidden" name="mode" value="new">
	</form>
	<?php endif;?>

<?php else: ?>
	<div class="alert alert-danger">このサーバーに登録できる<br>最大数に達しているため、現在登録できません。</div>
<?php endif; ?>
</div>
