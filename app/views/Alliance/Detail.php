<style>
.title::first-letter {
    color: inherit;
}
</style>
<?php
$title = <<<LF
<span style="color:{$alliance['color']}">{$alliance['mark']}</span> {$alliance['name']}
LF;
parent::pageTitle($title, '同盟情報 <a id="Edit"><span class="small label label-default" style="font-size:0.6em;color:#fff">編集</span></a>');
unset($title);

$cost = $init->costJoinAlly
    ? '<span class="cash">'.$init->costJoinAlly.$init->unitMoney.'</span>必要です。'
    : '必要ありません。';
$keep = $init->costKeepAlly
    ? '<span class="cash">'.$init->costKeepAlly.$init->unitMoney.'</span>必要です。<br>（維持費は毎ターン、同盟に所属する島で均等に負担されます）'
    : '必要ありません。';
?>
<div id="campInfo">

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
        <tr>
            <th class="NumberCell number" rowspan=2><?=$alliance['rank']?></th>
            <td class="NameCell lead" style="vertical-align:middle">
                <span class="islName"><span style="color:<?=$alliance['color']?>"><?=$alliance['mark']?></span> <?=$alliance['name']?></span>
            </td>
            <td class="InfoCell"><?=$alliance['number'].$init->nameSuffix?></td>
            <td class="InfoCell"><?=$alliance['score'].$init->unitPop?></td>
            <td class="InfoCell"><?=$alliance['occupation']?>%</td>
        </tr>
        <tr>
            <td class="CommentCell" colspan=4><span class="head"><?=$alliance['oName']?>： </span><?=$alliance['comment'] ?? ''?></td>
        </tr>
    </tbody>
</table>
</div>
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

<button id="JoinAlliance" type="button" class="btn btn-block btn-lg btn-primary">この同盟に参加する</button>

<hr>
<h2>所属する<?=$init->nameSuffix?>の情報</h2>

<div class="table-responsive">
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
            <td class="InfoCell"><?=$island['pop'].$init->unitPop?></td>
            <td class="InfoCell"><?=$island['area'].$init->unitArea?></td>
            <td class="InfoCell"><?=$island['money']?></td>
            <td class="InfoCell"><?=$island['food'].$init->unitFood?></td>
            <td class="InfoCell"><?=$island['farm']?></td>
            <td class="InfoCell"><?=$island['factory']?></td>
            <td class="InfoCell"><?=$island['commerce']?></td>
            <td class="InfoCell"><?=$island['mountain']?></td>
            <td class="InfoCell"><?=$island['hatuden']?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
</div>

<div id="Modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" method="post" action="<?=$this->this_file?>">
            <div class="modal-header">
                <h4 id="ModalTitle" class="modal-title"><span style="color:<?=$alliance['color']?>"><?=$alliance['mark']?></span> <?=$alliance['name']?>に参加する</h4>
            </div>
            <div id="ModalBody" class="modal-body">
                <div class="alert alert-info">
                    <p><strong class="text-danger">（注意）</strong></p>
                    <p>同盟への参加には費用が<?=$cost?></p>
                    <p>維持費は<?=$keep?></p>
                </div>
                <div class="form-group">
                    <label for="Whoami">あなたの<?=$init->nameSuffix?>名</label>
                    <select name="Whoami" class="form-control">
                        <?=$hako->islandList?>
                    </select>
                </div>
                <div class="form-group">
                    <label>パスワード</label>
                    <input type="password" name="Pwd" class="form-control" required>
                </div>
                <input type="number" name="JoinTo" value="<?=$data["ALLYID"]?>" hidden readonly>
            </div>
            <div id="ModalFooter" class="modal-footer">
                <button name="cancel" type="button" class="btn btn-default" data-dismiss="modal">やめる</button>
                <button type="submit" class="btn btn-primary">参加する</button>
            </div>
        </form>
    </div>
</div>
<div id="ModalBackdrop" class="modal-backdrop fade" style="display:none"></div>

<script>
<?php include 'Script/Detail.js';?>
</script>
<?php
unset($cost,$keep);
