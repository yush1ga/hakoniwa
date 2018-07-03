<?php
parent::pageTitle($init->title, '同盟管理ページ');
?>
<?php if ($init->allyUse > 0): ?>

<p><a href="<?=$this->this_file?>?p=register" class="btn btn-default">同盟の結成</a></p>

<?php endif; ?>

<h2>各同盟の状況</h2>

<?php $this->allyInfo($hako);
