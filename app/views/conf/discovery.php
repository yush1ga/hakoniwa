<div id="NewIsland">
	<h2>新しい島を探す</h2>

<?php if ($number < $init->maxIsland): ?>

	<?php if ($init->registerMode == 1 && $init->adminMode == 0): ?>

	<div class="alert alert-info">
        <p>当箱庭では、ゲーム参加に際して事前チェックを行っています。<br>参加希望の方は、管理者にその旨と「<strong>希望する<?=$init->nameSuffix?>名</strong>」を送信してください。</p>
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
			<input type="password" class="form-control" name="PASSWORD" size="32" required>
		</div>
		<div class="form-group">
			<label>パスワード（確認のためもう一度）</label>
			<input type="password" class="form-control" name="PASSWORD2" size="32" required>
		</div>

		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="探しに行く">
		</div>

		<input type="hidden" name="mode" value="new">
	</form>
	<?php endif;?>

<?php else:?>
	<div class="alert alert-danger"><p>現在、このサーバーの最大ユーザー数に達しているため登録できません。<br>詳しくは管理者にお問い合わせください。</p></div>
<?php endif;?>
</div>
