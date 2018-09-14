<?php
parent::pageTitle($init->title, '同盟管理ページ');
?>

<p>
<?php if ($init->allyUse > 0): ?>
<a href="<?=$this->this_file?>?p=register" class="btn btn-default">同盟の結成</a>
<?php endif; ?>
<?php if ($hako->allyNumber > 0):?>
<a id="Withdrawal" href="<?=$this->this_file?>?p=withdrawal" class="btn btn-danger">同盟の脱退</a>
<?php
// dump($hako);
$modal_body = <<<EOF
                <form action="">
                    <div class="form-group">
                        <label for="Whoami">あなたの{$init->nameSuffix}：</label>
                        <select name="Whoami" class="form-control">
                            $hako->islandList
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Pwd">パスワード：</label>
                        <input name="Pwd" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="Alliance">脱退する同盟：</label>

                    </div>
                    <div class="checkbox" style="font-size:1.2em">
                        <label style="padding-left:1.5em"><input name="Agree" type="checkbox" style="width:1.2em;height:1.2em;margin:0 0 0-1.5em"> 同盟「」から脱退する</label>
                    </div>
                </form>
EOF;

$modal_footer = <<<EOF
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">やめる</button>
                <button type="submit" class="btn btn-warning">実行</button>
EOF;

require VIEWS."Modal.php";
new Modal([
    "id"     => "ModalWithdrawal",
    "title"  => "脱退する",
    "body"   => $modal_body,
    "footer" => $modal_footer
]);

endif;?>
</p>

<h2>各同盟の状況</h2>

<?php $this->allyInfo($hako);

?>

<script><?php require "Script/Index.js";?></script>
<?php
