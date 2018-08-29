<form id="NoticeFromAdmin" method="post">
<h2>お知らせ <span id="js_editNoticeFromAdmin" class="label label-default" style="font-size:.4em;">編集<span class="sr-only">する</span></span></h2>
<textarea style="height:<?=$init->divHeight?>px;width:100%;border:0;resize:none;" autocomplete="off" readonly>
<?php
    (new Log())->infoPrint();
?>
</textarea>
<div class="form-inline" style="display:none">
    <div class="input-group">
        <input type="password" class="form-control" style="width:auto" placeholder="パスワード" disabled>
        <button id="js_submitNoticeFromAdmin" class="form-control input-group-addon" style="width:auto" autocomplete="off" disabled>更新</button>
    </div>
</div>
</form>
<script>
<?php require "info.js"; ?>
</script>
