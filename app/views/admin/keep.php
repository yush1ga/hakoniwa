<h1>島預かり管理ツール</h1>

<dl>
    <dt>「島預かり（島凍結）」とは？</dt>
    <dd>
        各ユーザーの島に対して管理人のみが適用できる設定で、島預かりが有効になった島は<strong>一切の時間経過がなくなります</strong>。<br>
        よって、街の成長・衰退、資金や食料の収入・支出、怪獣の出現・移動などの、ターンごとに行われる<strong>すべての更新が発生しなくなります</strong>。<br>
        また、この効果は他の島との交易に際しても有効のため、島預かり中の他島への援助・貿易・ミサイル発射などの交流要素はすべて失敗します。
    </dd>
</dl>

<?php if (isset($data['PASSWORD']) && Util::checkPassword('', $data['PASSWORD'])):?>

<h2>島預かり適用 <small>（島データを凍結）</small></h2>
<form action="<?=$this_file?>" method="post">
    <div class="form-group">
        <label for="ISLANDID">対象の島：</label>
        <select name="ISLANDID" required class="form-control">
            <?=$hako->islandListNoKP?>
        </select>
    </div>
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="TOKP">
    <button type="submit" class="btn btn-success">適用する</button>
</form>

<h2>島預かり解除 <small>（通常の島に変更）</small></h2>
<form action="<?=$this_file?>" method="post">
    <div class="form-group">
        <label for="ISLANDID">対象の島：</label>
        <select name="ISLANDID" required class="form-control">
            <?=$hako->islandListKP?>
        </select>
    </div>
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="FROMKP">
    <button type="submit" class="btn btn-danger">解除する</button>
</form>

<?php else:?>

<form action="<?=$this_file?>" method="post" class="form-inline">
    <label>パスワード：
    <input type="password" length="32" name="PASSWORD" class="form-control"></label>
    <input type="hidden" name="mode" value="enter">
    <button type="submit" class="btn btn-default">サインイン</button>
</form>

<?php endif;?>
