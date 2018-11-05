<?php declare(strict_types=1);
function remainTime(int $nextTime): string
{
    $remainSec = $nextTime - $_SERVER['REQUEST_TIME'];
    $echoVal = '<small>（次のターンまで、残りおよそ';

    $echoVal .= ($remainSec/86400 >= 1)? (floor($remainSec/86400).'日と'): '';
    $remainSec %= 86400;
    $echoVal .= ($remainSec/3600 >= 1)? (floor($remainSec/3600).'時間'): '';
    $remainSec %= 3600;
    $echoVal .= ceil($remainSec/60).'分）</small>';

    if ($remainSec <= 0) {
        $echoVal = '<div><button type="button" class="btn btn-danger btn-lg btn-block" onClick="location.reload()" style="white-space:normal;"><strong>ページを更新してください<br>ページが最新状態まで更新されない限り、全ての操作を行うことができません。</strong></button></div>';
    }
    /* "（次のターンまで、残りおよそx日とy時間z分）" or "ページを更新してください" */
    return $echoVal;
}
?>
<?=$this->pageTitle($init->title, 'トップ')?>

<div class="alert alert-danger">
<p>本サービスは<strong>オープンアルファ版</strong>であるため、以下をご承知おきください。</p>
<ul>
<li>仕様・デザイン・要求スペック・要求情報等、あらゆるものが予告なしに変更になる恐れがあります。</li>
<li>プレイデータは必ずしも保全されるものではありません。また、仮にデータが損失した際にも、データ復旧・損失補填などの措置は基本的にとりません。</li>
<li>不具合をご報告いただいても、必ずしもすぐに修正されるとは限りません。<br>不具合報告は <a href="https://github.com/Sotalbireo/hakoniwa/issues" target="_blank">こちらから (GitHub Issues)。</a></li>
<li>製作者都合により、予告なくサービスが終了することもあります。</li>
</ul>
</div>

<h2 class="Turn">ターン<?=$hako->islandTurn?></h2>

<?php if (($init->turnPrizeUnit - ($hako->islandTurn % $init->turnPrizeUnit)) < max(3, $init->turnPrizeUnit / 20)): ?>
<div class="alert alert-info" role="alert">
    <h2 class="text-primary">めざせ<?= ceil($hako->islandTurn / $init->turnPrizeUnit) * $init->turnPrizeUnit ?>ターン賞！</h2>
</div>
<?php endif ?>

<?php if (DEBUG): ?>
<div class="m-b-2">
    <form action="<?=$this_file?>" method="post">
        <input type="hidden" name="mode" value="debugTurn">
        <button type="submit" class="btn btn-danger">ターンを進める</button>
    </form>
</div>
<?php endif; ?>

<div class="lastModified">
    <p>最終更新時間： <?=date("Y年n月j日G時i分 (T)", (int)$hako->islandLastTime)?><br>
        <?=remainTime($hako->islandLastTime + $init->unitTime)?> <a class="btn btn-xs btn-default" href="#" onclick="window.location.reload();return !1;">ページ更新</a></p>
</div>

<hr>

<div class="row">
    <div class="col-sm-4">
<?php if ($hako->islandNumber > 0):?>
        <h2>自分の島へ</h2>

        <form action="<?=$this_file?>" method="post">
            <div class="form-group">
                <label>あなたの島の名前は？</label>
                <select name="ISLANDID" class="form-control">
                    <?=strip_tags($hako->islandList, '<option>')?>
                </select>
            </div>
            <div class="form-group">
                <label>パスワード</label>
                <input type="password" name="PASSWORD" class="form-control" value="<?=$defaultPassword?>" size="32" required>
            </div>

            <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" name="DEVELOPEMODE" value="cgi" id="cgi" <?=$radio?>>
                    通常モード
                </label>
                <label class="radio-inline">
                    <input type="radio" name="DEVELOPEMODE" value="javascript" id="javascript" <?=$radio2?>>
                    JavaScript高機能モード
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">開発しに行く</button>
                <input type="hidden" name="mode" value="owner">
            </div>
        </form>
<?php else:?>
        <p>現在、この海域に<?=$init->nameSuffix?>は見つかっていません</p>
        <p><a href="<?=$this_file?>?mode=conf" class="btn btn-primary">→新しい<?=$init->nameSuffix?>を探しにいく</a></p>
<?php endif;?>
    </div>

    <div class="col-sm-offset-1 col-sm-7">
<?php // 「お知らせ」
require_once VIEWS."/log/info.php";
?>
    </div>
</div>

<hr>

<?php if ($hako->islandNumber - $hako->islandNumberBF > 0) {
    require_once VIEWS."top/category-rank.php";
} elseif ($hako->islandNumber === 0) {
    // noop
} else {
    echo <<<EOT

    <p class="alert alert-info">現在、この海域に{$init->nameSuffix}は見つかっていません</p>
    <p><a href="{$this_file}?mode=conf" class="btn btn-primary">→新しい{$init->nameSuffix}を探しにいく</a></p>

EOT;
}?>

<?php if ($hako->allyNumber):?>
<section class="IslandView">
    <h2>同盟の状況</h2>
    <?php
        $alliance = new HtmlAlly;
        $alliance->allyInfo($hako);
    ?>
    <p>※同盟の名前から「同盟の情報」ページへ、盟主の<?=$init->nameSuffix?>名から「コメント変更」欄へ移動できます。</p>
</section>
<hr>
<?php endif;?>

<?php
if ($hako->islandNumber - $hako->islandNumberBF > 0) {
        require_once VIEWS."top/island-list.php";
    }

if ($hako->islandNumberBF !== 0) {
    require_once VIEWS."top/bf-list.php";
}

require_once VIEWS."log/history.php";

// 管理者登録モード
if ($init->registerMode) {
    require_once VIEWS."top/register-mode.php";
}

println("</div>");
