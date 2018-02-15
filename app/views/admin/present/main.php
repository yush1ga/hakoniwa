<h1>管理ツール： <small>プレゼント</small></h1>

<?php if ($this->mode=='enter' && $this->passCheck()): ?>
<h2>援助</h2>
<form action="<?=$this_file?>" method="post">
    <select name="ISLANDID"><?= $hako->islandList ?></select>に、
    資金：<input type="text" size="10" name="MONEY" value="0"><?=$init->unitMoney?>、
    食料：<input type="text" size="10" name="FOOD"  value="0"><?=$init->unitFood?>を
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="PRESENT">
    <input type="submit" value="資金／食料を援助">
</form>

<h2>制裁</h2>
<form action="<?=$this_file?>" method="post" name="InputPlan">
    <select name="ISLANDID" onchange="settarget(this);"><?=$hako->islandList?></select>の、（
    <select name="POINTX">
    <option value="0" selected>0</option>
    <?php for ($i = 1; $i < $init->islandSize; $i++):?>
        <option value="<?=$i?>"><?=$i?></option>
    <?php endfor;?>
    </select>,
    <select name="POINTY">
        <option value="0" selected>0</option>
    <?php for ($i = 1; $i < $init->islandSize; $i++):?>
        <option value="<?=$i?>"><?=$i?></option>
    <?php endfor;?>
    </select>）に、

    <select name="PUNISH">
        <option value="0">キャンセル</option>
        <option value="1">地震</option>
        <option value="2">津波</option>
        <option value="3">怪獣</option>
        <option value="4">地盤沈下</option>
        <option value="5">台風</option>
        <option value="6">巨大隕石（座標ランダム）</option>
        <option value="7">隕石（座標ランダム）</option>
        <option value="8">噴火（座標ランダム）</option>
    </select>を
    <input type="hidden" name="PASSWORD" value="<?=$data['PASSWORD']?>">
    <input type="hidden" name="mode" value="PUNISH">
    <input type="submit" value="災害発生"><br>
    <input type="button" value="目標捕捉" onClick="javascript: targetopen();">
</form>


<h2>現在のプレゼントリスト</h2>
<ul>
<?php
$hasPresent = false;

for ($i=0; $i < $hako->islandNumber; $i++) {
    $present =&$hako->islands[$i]['present'];
    $name =&$hako->islands[$i]['name'];
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
var w, p = 0;

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
<form action="<?=$this_file?>" method="post">
    <label>パスワード？：
    <input type="password" maxlength="32" name="PASSWORD"></label>
    <input type="hidden" name="mode" value="enter">
    <input type="submit" value="メンテナンス">
</form>
<?php endif;?>
