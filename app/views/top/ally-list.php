<div class="IslandView">
    <h2>同盟の状況</h2>
    <table class="table table-bordered">
        <col style="width:10%">
        <col style="width:60%">
        <colgroup span=3 style="width:10%"></colgroup>
    <thead>
        <tr>
            <th class="TitleCell head"><?=$init->nameRank?></th>
            <th class="TitleCell head">同盟</th>
            <th class="TitleCell head">所属数</th>
            <th class="TitleCell head">総人口</th>
            <th class="TitleCell head">占有率</th>
        </tr>
    </thead>
    <tbody>
<?php for ($i=0; $i<$hako->allyNumber; $i++): ?>
<?php
$ally = $hako->ally[$i];
$j = $i + 1;

$pop = 0;
for ($k=0; $k<$ally['number']; $k++) {
    $id = $ally['memberId'][$k];
    $pop += $hako->islands[$hako->idToNumber[$id]]['pop'];
}
$name = "<a href=\"{$allyfile}?AmiOfAlly={$ally['id']}\" title=\"「同盟の情報」ページへ\">{$ally['name']}</a>";
$pop = $pop . $init->unitPop;
?>
        <tr>
            <th class="NumberCell number" rowspan="2"><?=$j?></th>
            <td class="NameCell big"><span style="color:<?=$ally['color']?>"><?=$ally['mark']?></span> <?=$name?></td>
            <td class="InfoCell" style="vertical-align:middle"><?=$ally['number'].$init->nameSuffix?></td>
            <td class="InfoCell" style="vertical-align:middle"><?=$pop?></td>
            <td class="InfoCell" style="vertical-align:middle"><?=$ally['occupation']?>%</td>
        </tr>
        <tr>
            <td class="CommentCell" colspan=4><span class="head"><a href="<?=$allyfile?>?Allypact=<?=$ally['id']?>"><?=$ally['oName']?></a>： </span><?=$ally['comment']?>
            </td>
        </tr>
<?php endfor;?>
    </tbody>
</table>

<p>※同盟の名前から「同盟の情報」ページへ、盟主の<?=$init->nameSuffix?>名から「コメント変更」欄へ移動できます。</p>

</div>

<hr>
