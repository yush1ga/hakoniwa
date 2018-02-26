<h1 class="title"><?=$init->title?> <small>管理ツール：援助・制裁</small></h1>

<?php if (isset($data['PASSWORD']) && \Util::checkPassword('', $data['PASSWORD'])):?>

<h2>援助</h2>
<form action="<?=$this_file?>" method="post">
    <div class="form-group"><div class="form-inline">
        <label for="ISLANDID">対象の島：</label>
        <select name="ISLANDID" class="form-control">
            <?= $hako->islandList ?>
        </select>
    </div></div>
    <div class="form-group"><div class="form-inline">
        <label for="MONEY">資金：</label>
        <div class="input-group">
            <input type="number" size="10" min="0" name="MONEY" value="0" class="form-control">
            <div class="input-group-addon"><?=$init->unitMoney?></div>
        </div>
    </div></div>
    <div class="form-group"><div class="form-inline">
        <label for="FOOD">食料：</label>
        <div class="input-group">
            <input type="number" size="10" min="0" name="FOOD" value="0" class="form-control">
            <div class="input-group-addon"><?=$init->unitFood?></div>
        </div>
    </div></div>
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="PRESENT">
    <button type="submit" class="btn btn-success">資金／食料を援助</button>
</form>

<h2>制裁</h2>
<form action="<?=$this_file?>" method="post" name="InputPlan">
    <div class="form-group"><div class="form-inline">
        <label for="ISLANDID">対象の島：</label>
        <select name="ISLANDID" onchange="settarget(this);" class="form-control">
            <?=$hako->islandList?>
        </select>
    </div></div>
    <div class="form-group"><div class="form-inline">
        <label>座標：
            <button onclick="javascript: targetopen(); return !1;" class="btn btn-sm btn-basic">目標捕捉</button>
        </label>
        <p class="help-block">（x, y座標、それぞれ0～<?=$init->islandSize - 1?>）</p>
    </div></div>

    <div class="form-group"><div class="form-inline">
        <label>(x, y) = （</label>
        <input type="number" name="POINTX" min="0" max="<?=$init->islandSize - 1?>" value="0" class="form-control">
        <label>，</label>
        <input type="number" name="POINTY" min="0" max="<?=$init->islandSize - 1?>" value="0" class="form-control">
        <label>）</label>
    </div></div>

    <div class="form-group"><div class="form-inline">
        <label for="PUNISH">災害内容：</label>
        <select name="PUNISH" class="form-control">
            <option value="0">（援助・制裁キャンセル）</option>
            <option value="1">地震</option>
            <option value="2">津波</option>
            <option value="3">怪獣</option>
            <option value="4">地盤沈下</option>
            <option value="5">台風</option>
            <option value="6">巨大隕石（座標ランダム）</option>
            <option value="7">隕石（座標ランダム）</option>
            <option value="8">噴火（座標ランダム）</option>
        </select>
    </div></div>
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="PUNISH">
    <button type="submit" class="btn btn-danger">災害発生</button>
</form>


<h2>現在のプレゼントリスト</h2>
<ul>
<?php
$hasPresent = false;

for ($i=0; $i < $hako->islandNumber; $i++) {
    $present = &$hako->islands[$i]['present'];
    $name = &$hako->islands[$i]['name'];
    if ($present['item'] == 0) {
        if ($present['px'] != 0) {
            println('<li>', $init->tagName_.$name.$init->nameSuffix.$init->_tagName, 'に、資金<strong>', $present['px'].$init->unitMoney, '</strong></li>');
            $hasPresent = true;
        }
        if ($present['py'] != 0) {
            println('<li>', $init->tagName_.$name.$init->nameSuffix.$init->_tagName, 'に、食料<strong>', $present['py'].$init->unitFood, '</strong></li>');
            $hasPresent = true;
        }
    } elseif ($present['item'] > 0) {
        $items = ['地震','津波','怪獣','地盤沈下','台風','巨大隕石','隕石','噴火'];
        $item = $items[$present['item'] - 1];
        if ($present['item'] < 9) {
            $point = ($present['item'] < 6)? '' : "（{$present['px']},{$present['py']}）";
            println('<li>', $init->tagName_.$name.$init->nameSuffix.$point.$init->_tagName, 'に、', $init->tagDisaster_.$item.$init->_tagDisaster.'</li>');
            $hasPresent = true;
        }
    }
}
if (!$hasPresent) {
    println("<li>なし</li>");
}
?>
</ul>



<script>
let w, p = 0;

function settarget(part){
    p = part.options[part.selectedIndex].value;
}

function targetopen(){
    if(w==null || w.closed){
        w = window.open("<?=$main_file?>?target=" + p, "","width=<?=$width?>,height=<?=$height?>,scrollbars=1,resizable=1");
    }else{
        w.focus();
    }
}
</script>

<?php else:?>

<form action="<?=$this_file?>" method="post" class="form-inline">
    <label>パスワード：
    <input type="password" length="32" name="PASSWORD" class="form-control"></label>
    <input type="hidden" name="mode" value="enter">
    <button type="submit" class="btn btn-default">メンテナンス</button>
</form>

<?php endif;?>
