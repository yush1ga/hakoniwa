<?php declare(strict_types=1);
if ($alliances_number === 0): ?>

<p>現在、同盟はありません。</p>

<?php else: ?>

<p>占有率とは、同盟に加盟している<?=$init->nameSuffix?>の<strong>総人口</strong>をもとに算出された指標です。</p>

<div class="table-responsive">
<table class="table table-bordered">
    <colgroup>
        <col style="width:4em">
        <col style="width:auto">
        <colgroup span=3 style="width:12%"></colgroup>
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
            <td class="NameCell lead" style="vertical-align:middle">
<?php if (is_int(filter_input(INPUT_GET, 'detail', FILTER_VALIDATE_INT, ['min_range' => 0]))):?>
                <span class="islName"><span style="color:<?=$alliance['color']?>"><?=$alliance['mark']?></span> <?=$alliance['name']?></span>
<?php else:?>
                <a href="<?=$this->this_file?>?detail=<?=$alliance['id']?>" class="islName"><span style="color:<?=$alliance['color']?>"><?=$alliance['mark']?></span> <?=$alliance['name']?></a>
<?php endif;?>
            </td>
            <td class="InfoCell"><?=$alliance['members'] . $init->nameSuffix?></td>
            <td class="InfoCell"><?=$alliance['population'] . $init->unitPop?></td>
            <td class="InfoCell"><?=$alliance['occupation']?>%</td>
        </tr>
        <tr>
            <td class="CommentCell" colspan=4><span class="head"><?=$alliance['owner']?>： </span><?=$alliance['comment'] ?? ''?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php
