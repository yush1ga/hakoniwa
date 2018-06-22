<?php if ($alliances_number === 0): ?>

<p>現在、同盟はありません。</p>

<?php else: ?>

<p>占有率とは、同盟に加盟している<?=$init->nameSuffix?>の<strong>総人口</strong>をもとに算出された指標です。</p>

<div id="IslandView" class="table-responsive">
<table class="table table-bordered">
    <colgroup>
        <col style="width:4em">
    </colgroup>
    <thead>
    <tr>
        <th class="TitleCell head"><?=$init->nameRank?></th>
        <th class="TitleCell head">同盟</th>
        <th class="TitleCell head">所属する<?=$init->nameSuffix?>の数</th>
        <th class="TitleCell head">総人口</th>
        <th class="TitleCell head">占有率</th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($alliances as $i => $alliance): ?>
        <tr>
            <th class="NumberCell number" rowspan=2><?=$i+1?></th>
            <td class="NameCell lead" rowspan=2 style="vertical-align:middle"><a href="<?=$this->this_file?>?detail=<?=$alliance['id']?>" class="islName"><span style="color:<?=$alliance['color']?>"><?=$alliance['mark']?></span> <?=$alliance['name']?></a></td>
            <td class="InfoCell"><?=$alliance['members'] . $init->nameSuffix?></td>
            <td class="InfoCell"><?=$alliance['population'] . $init->unitPop?></td>
            <td class="InfoCell"><?=$alliance['occupation']?>%</td>
        </tr>
        <tr>
            <td class="CommentCell" colspan=3><span class="head"><?=$alliance['owner']?>： </span><?=$alliance['comment']?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php
