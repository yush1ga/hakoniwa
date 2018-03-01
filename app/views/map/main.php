<h1 class="text-center"><span class="islName"><?=$name?></span>へようこそ！</h1>

<?php
    // 情報
    $this->islandInfo($island, $number, 0);
    // マップ
    $this->islandMap($hako, $island, 0);
?>

<form action="<?=$this_file?>" method="get" class="text-center">
    <div class="form-group"><div class="form-inline">
        <select name="Sight" class="form-control">
            <?= strip_tags($hako->islandList, '<option>') ?>
        </select>
        <button type="submit" class="btn btn-default">→観光</button>
    </div></div>
</form>

<?php
    // 近況
    $this->islandRecent($island, 0);
