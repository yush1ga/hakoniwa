<style>
.title::first-letter {
    color: inherit;
}
</style>
<?php
$title = <<<LF
<span style="color:{$alliance['color']}">{$alliance['mark']}</span> {$alliance['name']}
LF;
parent::pageTitle($title, '同盟情報');
unset($title);

?>
<div id="campInfo">

<p>占有率とは、同盟に加盟している<?=$init->nameSuffix?>の<strong>総人口</strong>をもとに算出された指標です。</p>

<div id="IslandView" class="table-responsive">
<table class="table table-bordered">
    <colgroup>
        <col style="width:4em">
        <col style="width:auto">
    </colgroup>
    <colgroup span=3 style="width:10%"></colgroup>
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
        <tr>
            <th class="NumberCell number" rowspan=2><?=$i+1?></th>
            <td class="NameCell lead" rowspan=2 style="vertical-align:middle">
                <span class="islName"><span style="color:<?=$alliance['color']?>"><?=$alliance['mark']?></span> <?=$alliance['name']?></span>
            </td>
            <td class="InfoCell"><?=$alliance['members'].$init->nameSuffix?></td>
            <td class="InfoCell"><?=$alliance['population'].$init->unitPop?></td>
            <td class="InfoCell"><?=$alliance['occupation']?>%</td>
        </tr>
        <tr>
            <td class="CommentCell" colspan=3><span class="head"><?=$alliance['owner']?>： </span><?=$alliance['comment'] ?? ''?></td>
        </tr>
    </tbody>
</table>
</div>

<?php if ($alliance['message'] !== ''): ?>
<hr>
<div class="row">
    <div class="col-sm-offset-2 col-sm-8">
        <table class="table table-bordered">
            <tr><th class="TitleCell head text-center"><?=$alliance['title']?></th></tr>
            <tr><td class="CommentCell"><?=$alliance['message']?></td></tr>
        </table>
    </div>
</div>

<?php endif;?>
<hr>
<h2>所属する<?=$init->nameSuffix?>の情報</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th class="TitleCell head"><?=$init->nameRank?></th>
            <th class="TitleCell head"><?=$init->nameSuffix?></th>
            <th class="TitleCell head"><?=$init->namePopulation?></th>
            <th class="TitleCell head"><?=$init->nameArea?></th>
            <th class="TitleCell head"><?=$init->nameFunds?></th>
            <th class="TitleCell head"><?=$init->nameFood?></th>
            <th class="TitleCell head"><?=$init->nameFarmSize?></th>
            <th class="TitleCell head"><?=$init->nameFactoryScale?></th>
            <th class="TitleCell head"><?=$init->nameCommercialScale?></th>
            <th class="TitleCell head"><?=$init->nameMineScale?></th>
            <th class="TitleCell head"><?=$init->namePowerPlantScale?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($islands as $island): ?>
        <tr>
            <th class="NumberCell number"><?=$island['rank']?></th>
            <td class="NameCell"><?=$island['name']?></td>
            <td class="InfoCell"><?=$island['pop'] . $init->unitPop?></td>
            <td class="InfoCell"><?=$island['area'] . $init->unitArea?></td>
            <td class="InfoCell"><?=$island['money']?></td>
            <td class="InfoCell"><?=$island['food'] . $init->unitFood?></td>
            <td class="InfoCell"><?=$island['farm']?></td>
            <td class="InfoCell"><?=$island['factory']?></td>
            <td class="InfoCell"><?=$island['commerce']?></td>
            <td class="InfoCell"><?=$island['mountain']?></td>
            <td class="InfoCell"><?=$island['hatuden']?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php
