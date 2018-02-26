<h1 class="title"><?=$init->title?> <small>管理ツール：バトルフィールド</small></h1>

<?php if (isset($data['PASSWORD']) && \Util::checkPassword('', $data['PASSWORD'])):?>

<h2>通常の島からバトルフィールドに変更</h2>
<form action="<?=$this_file?>" method="post">
    <div class="form-group"><div class="form-inline">
        <label for="ISLANDID">対象の島：</label>
        <select name="ISLANDID" class="form-control">
            <?=$hako->islandListNoBF?>
        </select>
    </div></div>
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="TOBF">
    <button type="submit" class="btn btn-danger">バトルフィールドに変更</button>
</form>

<h2>バトルフィールドから通常の島に変更</h2>
<form action="<?=$this_file?>" method="post">
    <div class="form-group"><div class="form-inline">
        <label for="ISLANDID">対象の島：</label>
        <select name="ISLANDID" class="form-control">
            <?=$hako->islandListBF?>
        </select>
    </div></div>
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="FROMBF">
    <button type="submit" class="btn btn-success">通常の島に変更</button>
</form>

<?php else:?>

<form action="<?=$this_file?>" method="post" class="form-inline">
    <label>パスワード：
    <input type="password" length="32" name="PASSWORD" class="form-control"></label>
    <input type="hidden" name="mode" value="enter">
    <button type="submit" class="btn btn-default">サインイン</button>
</form>

<?php endif;?>
