<?php declare(strict_types=1);
$estb = $init->costMakeAlly
    ? '<span class="cash">'.$init->costMakeAlly.$init->unitMoney.'</span>必要です。<br>（ただし、管理者権限を利用した場合は費用がかかりません。）'
    : '必要ありません。';
$keep = $init->costKeepAlly
    ? '<span class="cash">'.$init->costKeepAlly.$init->unitMoney.'</span>必要です。<br>（維持費は毎ターン、同盟に所属する島で均等に負担されます）'
    : '必要ありません。';
?>
<style>

</style>

<h2>同盟の結成</h2>
<?php if ($init->allyUse === 2):?>
<div class="alert alert-danger">
    <p class="lead">設定により、現在GM（管理者）以外の同盟結成は禁止されています。</p>
</div>
<?php endif;?>
<div class="alert alert-info">
    <p><strong class="text-danger">（注意）</strong></p>
    <p>同盟の結成には費用が<?= $estb ?></p>
    <p>維持費は<?= $keep ?></p>
</div>

<form id="Establishment" action="<?=$this->this_file?>?p=confirm" method="post">
<h3>あなた（同盟主）の情報</h3>
<div class="form-horizontal">
    <div class="form-group">
        <label for="Whoami" class="col-sm-2 control-label"><?= $init->nameSuffix ?>名</label>
        <div class="col-sm-10">
            <div class="input-group">
                <select name="Whoami" class="form-control">
                <?php /* foreach ($variable as $key => $value):
                    <option><?= $value ?></option>
                <?php endforeach*/ ?>
                <?= $hako->islandList ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="Password" class="col-sm-2 control-label">パスワード</label>
        <div class="col-sm-10"><input type="password" name="Password" class="form-control" required pattern="\w+"></div>
    </div>
</div>
<h3>新しく作る同盟の情報</h3>
<div class="form-horizontal">
    <div class="form-group">
        <label for="AllianceSign" class="col-sm-2 control-label">記章</label>
        <div class="col-sm-10">
            <div class="form-inline">
                <select name="AllianceSign" class="form-control" aria-describedby="AllianceSignHelp">
                <?php foreach ($init->allyMark as $value): ?>
                    <option><?=$value?></option>
                <?php endforeach ?>
                </select>
            </div>
            <p name="AllianceSignHelp" class="help-block">すでにある同盟と同じ記章は利用できません。<br>ご利用の端末やブラウザ、フォントなどの影響によって見た目が変化することがありますが、そういうものです。</p>
        </div>
    </div>
    <div class="form-group">
        <label for="AllianceColor" class="col-sm-2 control-label">色</label>
        <div class="col-sm-4">
            <input type="color" value="#000000" name="AllianceColor" class="form-control" maxlength=7 pattern="^#[0-9a-fA-F]{6}$" required>
        </div>
    </div>
    <div class="form-group">
        <label for="AllianceName" class="col-sm-2 control-label">名前</label>
        <div class="col-sm-10">
            <input type="text" value="" placeholder="サンプル" name="AllianceName" class="form-control" pattern="\w{1,}" required aria-describedby="AllianceNameHelp">
            <p id="AllianceNameHelp" class="help-block">利用できない文字・単語については<a href="https://github.com/Sotalbireo/hakoniwa/wiki/FAQ#島名同盟名に使えない文字単語" target="_blank">こちら</a>を参照ください（別サイトが開きます）。</p>
        </div>
    </div>
</div>
<h4 class="text-center">表示サンプル</h4>
<div id="AllianceSample" class="text-center">
    <div class="panel panel-default lead" style="display:inline-block;font-weight:bold;padding:0 1em;">
        <p class="m-b-0"></p>
    </div>
</div>
<div class="row">
<?php if ($init->allyUse): ?>
    <button type="button" name="Confirm" class="col-sm-offset-4 col-sm-4 btn btn-info">確認</button>
<?php else: ?>
    <button type="button" class="col-sm-offset-4 col-sm-4 btn btn-default" disabled onclick="return !1;">設定により同盟を結成できません</button>
<?php endif; ?>
</div>
</form>

<div id="Modal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">確認</h4>
            </div>
            <div id="ModalBody" class="modal-body"></div>
            <div class="modal-footer">
                <button name="cancel" type="button" class="btn btn-default" data-dismiss="modal">もどる</button>
                <button type="submit" class="btn btn-primary" name="mode" value="establish" form="Establishment" formaction="<?=$this->this_file?>" disabled>登録する</button>
            </div>
        </div>
    </div>
</div>
<div id="ModalBackdrop" class="modal-backdrop fade" style="display:none"></div>

<script charset="utf-8">
;(function(){
const denyingNameWords = <?=$denying_name_words?>;
const regexDenyingNameWords = <?=$regex_denying_name_words?>;
<?php require 'script/Alliance.js'; ?>
})();
</script>
<?php
