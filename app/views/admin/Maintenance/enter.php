<?php if (file_exists($init->passwordFile)):?>

<form action="<?=$this_file?>" method="post">
    <div class="form-group"><div class="form-inline">
        <label for="PASSWORD">パスワード：</label>
        <input type="password" size="64" name="PASSWORD" class="form-control">
    </div></div>
    <input type="hidden" name="mode" value="enter">
    <button type="submit" class="btn btn-default">サインイン</button>
</form>

<?php else:?>

<h2>初期設定</h2>

<p class="lead"><strong>マスタパスワードと特殊パスワードを決めてください。</strong></p>
<p>マスタパスワードは、管理者権限でログインするために必要になります。<br>
特殊パスワードは、マップエディットで資源を増加させる際などに用います。<br>
※入力ミスを防ぐために、それぞれ２回ずつ入力してください。</p>

<form action="<?=$this_file?>" method="post">

    <h3>マスタパスワード：</h3>
        <div class="form-group">
            <div class="form-inline">
                <label>(1) <input type="password" name="MPASS1" value="" size=32 class="form-control"></label>
            </div>
            <div class="form-inline">
                <label>(2) <input type="password" name="MPASS2" value="" size=32 class="form-control"></label>
            </div>
        </div>

    <h3>特殊パスワード：</h3>
        <div class="form-group">
            <div class="form-inline">
                <label>(1) <input type="password" name="SPASS1" value="" size=32 class="form-control"></label>
            </div>
            <div class="form-inline">
                <label>(2) <input type="password" name="SPASS2" value="" size=32 class="form-control"></label>
        </div>

    <hr style="margin:16px;border:0;padding:0;">

    <input type="hidden" name="mode" value="setup">
    <input type="submit" value="パスワードを設定する" class="btn btn-primary">
</form>

<?php endif;?>
