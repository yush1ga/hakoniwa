<?php
/**
 * 箱庭諸島 S.E - 島編集用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once 'config.php';
require_once MODEL.'/hako-file.php';
require_once PRESENTER.'/hako-html.php';

$init = new \Hakoniwa\Init;

$THIS_FILE = $init->baseDir . '/hako-edit.php';

class CgiImitation
{
    public $mode = "";
    public $dataSet = [];
    //---------------------------------------------------
    // POST、GETのデータを取得
    //---------------------------------------------------
    public function parseInputData(): void
    {
        global $init;

        $this->mode = $_POST['mode'] ?? "";

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->dataSet[$key] = str_replace(",", "", $value);
            }
            if (!empty($_POST['Sight'])) {
                $this->dataSet['ISLANDID'] = $_POST['Sight'];
            }
        }
    }

    //---------------------------------------------------
    // COOKIEを取得
    //---------------------------------------------------
    public function getCookies(): void
    {
        if (!empty($_COOKIE)) {
            foreach ($_COOKIE as $name => $value) {
                switch ($name) {
                    case "POINTX":
                        $this->dataSet['defaultX'] = $value;

                        break;
                    case "POINTY":
                        $this->dataSet['defaultY'] = $value;

                        break;
                    case "LAND":
                        $this->dataSet['defaultLAND'] = $value;

                        break;
                    case "MONSTER":
                        $this->dataSet['defaultMONSTER'] = $value;

                        break;
                    case "SHIP":
                        $this->dataSet['defaultSHIP'] = $value;

                        break;
                    case "LEVEL":
                        $this->dataSet['defaultLEVEL'] = $value;

                        break;
                    case "IMG":
                        $this->dataSet['defaultImg'] = $value;

                        break;
                }
            }
        }
    }

    //---------------------------------------------------
    // COOKIEを生成
    //---------------------------------------------------
    public function setCookies(): void
    {
        $time = $_SERVER['REQUEST_TIME'] + 90; // 90秒間有効

        // Cookieの設定 & POSTで入力されたデータで、Cookieから取得したデータを更新
        if (isset($this->dataSet['POINTX'])) {
            setcookie("POINTX", $this->dataSet['POINTX'], $time);
            $this->dataSet['defaultX'] = $this->dataSet['POINTX'];
        }
        if (isset($this->dataSet['POINTY'])) {
            setcookie("POINTY", $this->dataSet['POINTY'], $time);
            $this->dataSet['defaultY'] = $this->dataSet['POINTY'];
        }
        if (isset($this->dataSet['LAND'])) {
            setcookie("LAND", $this->dataSet['LAND'], $time);
            $this->dataSet['defaultLAND'] = $this->dataSet['LAND'];
        }
        if (isset($this->dataSet['MONSTER'])) {
            setcookie("MONSTER", $this->dataSet['MONSTER'], $time);
            $this->dataSet['defaultMONSTER'] = $this->dataSet['MONSTER'];
        }
        if (isset($this->dataSet['SHIP'])) {
            setcookie("SHIP", $this->dataSet['SHIP'], $time);
            $this->dataSet['defaultSHIP'] = $this->dataSet['SHIP'];
        }
        if (isset($this->dataSet['LEVEL'])) {
            setcookie("LEVEL", $this->dataSet['LEVEL'], $time);
            $this->dataSet['defaultLEVEL'] = $this->dataSet['LEVEL'];
        }
        if (isset($this->dataSet['IMG'])) {
            setcookie("IMG", $this->dataSet['IMG'], $time);
            $this->dataSet['defaultImg'] = $this->dataSet['IMG'];
        }
    }
}

//----------------------------------------------------------------------
class Edit
{
    //---------------------------------------------------
    // TOP 表示（パスワード入力）
    //---------------------------------------------------
    public function enter(): void
    {
        global $init;

        echo <<<END
<h1 class="title">$init->title <small>マップエディタ</small></h1>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
    <label for="PASSWORD">パスワード：</label>
    <div class="form-inline">
        <input type="password" size="32" name="PASSWORD" class="form-control">
        <input type="hidden" name="mode" value="enter">
        <button type="submit" class="btn btn-default">一覧へ</button>
    </div>
</form>
END;
    }

    //---------------------------------------------------
    // 島の一覧を表示
    //---------------------------------------------------
    public function main($hako, $data): void
    {
        global $init;

        // パスワード
        if (!Util::checkPassword("", $data['PASSWORD'])) {
            // password間違い
            HakoError::wrongPassword();

            return;
        }

        echo <<<END
<h1 class="title">マップエディタ</h1>
<h2 class="Turn">ターン$hako->islandTurn</h2>
<hr>
<div id="IslandView">
<h2>諸島の状況</h2>
<p>島の名前をクリックするとマップエディタが表示されます。</p>

<table class="table table-bordered table-condensed">
<thead>
    <tr>
        <th class="TitleCell head">$init->nameRank</th>
        <th class="TitleCell head">$init->nameSuffix</th>
        <th class="TitleCell head">$init->namePopulation</th>
        <th class="TitleCell head">$init->nameArea</th>
        <th class="TitleCell head">$init->nameFunds</th>
        <th class="TitleCell head">$init->nameFood</th>
        <th class="TitleCell head">$init->nameFarmSize</th>
        <th class="TitleCell head">$init->nameFactoryScale</th>
        <th class="TitleCell head">$init->nameMineScale</th>
    </tr>
</thead>
END;
        // 表示内容は、管理者用の内容
        for ($i = 0; $i < $hako->islandNumber; $i++) {
            $island = $hako->islands[$i];
            $j = $island['isBF'] ? '★' : $i + 1;
            $id = $island['id'];
            $pop = $island['pop'] . $init->unitPop;
            $area = $island['area'] . $init->unitArea;
            $money = $island['money'] . $init->unitMoney;
            $food = $island['food'] . $init->unitFood;
            $farm = ($island['farm'] <= 0) ? $init->notHave : $island['farm'] * 10 . $init->unitPop;
            $factory = ($island['factory'] <= 0) ? $init->notHave : $island['factory'] * 10 . $init->unitPop;
            $mountain = ($island['mountain'] <= 0) ? $init->notHave : $island['mountain'] * 10 . $init->unitPop;
            $comment = $island['comment'];
            $monster = ($island['monster'] > 0) ? '<strong class="monster">[怪獣'.$island['monster'].'体]</strong>' : '';
            $name = ($island['absent'] == 0) ? $init->tagName_.$island['name'].$init->nameSuffix.$init->_tagName : $init->tagName2_.$island['name'].$init->nameSuffix.'('.$island['absent'].')'.$init->_tagName2;
            $owner = $island['owner'] ?: "anonymous";

            if ($hako->islandNumber - $i == $hako->islandNumberBF) {
                echo <<<END
</table>
</div>
<hr>
<div id="IslandView">
<h2>Battle Fieldの状況</h2>
<table class="table table-bordered">
<thead>
    <tr>
        <th class="TitleCell head">$init->nameRank</th>
        <th class="TitleCell head">$init->nameSuffix</th>
        <th class="TitleCell head">$init->namePopulation</th>
        <th class="TitleCell head">$init->nameArea</th>
        <th class="TitleCell head">$init->nameFunds</th>
        <th class="TitleCell head">$init->nameFood</th>
        <th class="TitleCell head">$init->nameFarmSize</th>
        <th class="TitleCell head">$init->nameFactoryScale</th>
        <th class="TitleCell head">$init->nameMineScale</th>
    </tr>
</thead>
END;
            }
            echo <<<END
    <tr>
        <th class="NumberCell number" rowspan=2>$j</th>
        <td class="NameCell" rowspan=2><a href="#" onClick="document.MAP{$id}.submit();return !1;">$name</a> $monster</td>
<form name="MAP{$id}" action="{$GLOBALS['THIS_FILE']}" method="post">
	<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
	<input type="hidden" name="mode" value="map">
	<input type="hidden" name="Sight" value="{$id}">
</form>
        <td class="InfoCell">$pop</td>
        <td class="InfoCell">$area</td>
        <td class="InfoCell">$money</td>
        <td class="InfoCell">$food</td>
        <td class="InfoCell">$farm</td>
        <td class="InfoCell">$factory</td>
        <td class="InfoCell">$mountain</td>
    </tr>
    <tr>
        <td class="CommentCell" colspan=7><span class="head">{$owner}：</span> $comment</td>
    </tr>
END;
        }
        println('</table>', "\n", '</div>');
    }

    /**
     * マップエディタの表示
     * @param  [type] $hako [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function editMap($hako, $data)
    {
        global $init;

        // パスワード
        if (!Util::checkPassword("", $data['PASSWORD'])) {
            HakoError::wrongPassword();

            return;
        }
        $html = new HtmlMap();
        $id = $data['ISLANDID'];
        $number = $hako->idToNumber[$id];
        $island = $hako->islands[$number];

        // 地形リストを生成
        $landList = [
            "$init->landSea",
            "$init->landSeaSide",
            "$init->landWaste",
            "$init->landPoll",
            "$init->landPlains",
            "$init->landForest",
            "$init->landTown",
            "$init->landProcity",
            "$init->landNewtown",
            "$init->landBigtown",
            "$init->landSeaCity",
            "$init->landFroCity",
            "$init->landPort",
            "$init->landShip",
            "$init->landRail",
            "$init->landStat",
            "$init->landTrain",
            "$init->landFusya",
            "$init->landSyoubou",
            "$init->landSsyoubou",
            "$init->landFarm",
            "$init->landSfarm",
            "$init->landNursery",
            "$init->landFactory",
            "$init->landCommerce",
            "$init->landMountain",
            "$init->landHatuden",
            "$init->landBase",
            "$init->landHaribote",
            "$init->landDefence",
            "$init->landSbase",
            "$init->landSdefence",
            "$init->landMyhome",
            "$init->landSoukoM",
            "$init->landSoukoF",
            "$init->landMonument",
            "$init->landSoccer",
            "$init->landPark",
            "$init->landSeaResort",
            "$init->landOil",
            "$init->landBank",
            "$init->landMonster",
            "$init->landSleeper",
            "$init->landZorasu"
        ];

        // 地形リスト名称を生成
        $landName = [
            "海、浅瀬",
            "砂浜",
            "荒地",
            "汚染土壌",
            "平地",
            "森",
            "村、町、都市",
            "防災都市",
            "ニュータウン",
            "現代都市",
            "海底都市",
            "海上都市",
            "港",
            "船舶",
            "線路",
            "駅",
            "電車",
            "風車",
            "消防署",
            "海底消防署",
            "農場",
            "海底農場",
            "養殖場",
            "工場",
            "商業ビル",
            "山、採掘場",
            "発電所",
            "ミサイル基地",
            "ハリボテ",
            "防衛施設",
            "海底基地",
            "海底防衛施設",
            "マイホーム",
            "金庫",
            "食料庫",
            "記念碑",
            "スタジアム",
            "遊園地",
            "海の家",
            "海底油田",
            "銀行",
            "怪獣",
            "怪獣（睡眠中）",
            "ぞらす"
        ];
        echo <<<END
<script type="text/javascript">
function ps(x, y, ld, lv) {
    lv = lv === undefined ? 0 : lv;
	document.forms.InputPlan.POINTX.options[x].selected = true;
	document.forms.InputPlan.POINTY.options[y].selected = true;
    if (ld !== undefined)
        document.forms.InputPlan.LAND.options[ld].selected = true;

	if(ld == {$init->landMonster} || ld == {$init->landSleeper}) {
		mn = Math.floor(lv / 10);
		lv = lv - mn * 10;
		document.InputPlan.MONSTER.options[mn].selected = true;
    }
	document.forms.InputPlan.LEVEL.value = lv;
	return true;
}
</script>

<h1><span class="islName">{$island['name']}$init->nameSuffix</span> <small>マップエディタ <button form="TOP" type="submit" class="btn btn-xs btn-link">リストに戻る</button></small></h1>
<form id="TOP" action="{$GLOBALS['THIS_FILE']}" method="post">
    <input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
    <input type="hidden" name="mode" value="enter">
</form>
END;
        // 島の情報を表示
        $html->islandInfo($island, $number, 1);

        // 説明文を表示
        echo <<<END
<ul>
    <li>一度変更したら<strong>元に戻すことはできません</strong>。十分に注意してください（再変更して誤魔化すことはできます）。</li>
    <li>入力内容によっては、<strong>島データを破壊する恐れがあります</strong>。バックアップを確認してから行ってください。</li>
    <li>ここでの変更は地形データに対してのみ即時反映され、他のデータへは即時反映されません。ターン更新の際に反映されます。</li>
</ul>

<table class="table table-bordered">
<tr valign="top">
<td class="MapCell">
END;
        // 地形出力
        $html->islandMap($hako, $island, 1);

        echo <<<END
</td>
<td class="InputCell">
	<form action="{$GLOBALS['THIS_FILE']}" method="post" id="InputPlan">
		<input type="hidden" name="mode" value="regist">
		<input type="hidden" name="ISLANDID" value="{$island['id']}">
		<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">

        <div class="form-inline">
            <label for="POINTX">座標（</label>
            <select name="POINTX" class="form-control">
END;
        for ($i = 0; $i < $init->islandSize; $i++) {
            $sel = $i == ($data['defaultX'] ?? '') ? ' selected' : '';
            println('<option', $sel, '>', $i, '</option>');
        }
        echo '</select>，<select name="POINTY" class="form-control">';
        for ($i = 0; $i < $init->islandSize; $i++) {
            $sel = $i == ($data['defaultY'] ?? '') ? ' selected' : '';
            println('<option', $sel, '>', $i, '</option>');
        }
        echo <<<END
            </select><strong>）</strong>
        </div>
        <hr>
        <div class="form-group">
            <label for="LAND">地形</label>
            <select name="LAND" class="form-control">
END;
        for ($i = 0, $c = count($landList); $i < $c; $i++) {
            $sel = $landList[$i] == ($data['defaultLAND'] ?? '') ? ' selected' : '';
            println('<option value="', $landList[$i], '"', $sel, '>', $landName[$i], '</option>');
        }
        echo <<<END
            </select>
        </div>
        <hr>
        <div class="form-group">
            <label for="MONSTER">怪獣の種類</label>
            <select name="MONSTER" class="form-control">
END;
        for ($i = 0; $i < $init->monsterNumber; $i++) {
            $sel = $i == ($data['defaultMONSTER'] ?? '') ? ' selected' : '';
            println('<option value="', $i, '"', $sel, '>', $init->monsterName[$i], '</option>');
        }
        echo <<<END
            </select>
        </div>
        <hr>
        <div class="form-group">
            <label for="SHIP">船舶の種類</label>
            <select name="SHIP" class="form-control">
END;
        for ($i = 0, $c=count($init->shipName); $i < $c; $i++) {
            if ($init->shipName[$i] != "") {
                $sel = $i == ($data['defaultSHIP'] ?? '') ? ' selected' : '';
                println('<option value="', $i, '"', $sel, '>', $init->shipName[$i], '</option>');
            }
        }

        $value = $data['defaultLEVEL'] ?? 0;
        echo <<<END
            </select>
        </div>
        <hr>
        <div class="form-group">
            <label for="LEVEL">レベル</label>
            <input type="number" min="0" max="255" maxlength="4" name="LEVEL" value="$value" class="form-control">
        </div>
        <strong>レベルについて</strong>
        <dl>
            <dt>海、浅瀬</dt>
            <dd>0：海<br>1：浅瀬<br>2～：財宝の埋まった海</dd>
            <dt>荒地</dt>
            <dd>0：普通の荒地<br>1：ミサイル痕のある荒地</dd>
            <dt>街系</dt>
            <dd>～30：村<br>～100：町<br>～200：都市<br>200～：大都市</dd>
            <dt>ミサイル基地・海底基地</dt>
            <dd>経験値</dd>
            <dt>山、採掘場</dt>
            <dd>1～：採掘場</dd>
            <dt></dt>
            <dd></dd>
        </dl>
        <hr>
        <button type="submit" class="btn btn-info btn-block">変更</button>
    </form>
</td>
</tr>
</table>
END;
    }

    /**
     * 地形の登録
     * @param  [type] $hako [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function register($hako, $data)
    {
        global $init;

        // パスワード
        if (!Util::checkPassword("", $data['PASSWORD'])) {
            HakoError::wrongPassword();

            return;
        }

        $id = $data['ISLANDID'];
        $number = $hako->idToNumber[$id];
        $island = $hako->islands[$number];
        $land = &$island['land'];
        $landValue = &$island['landValue'];
        $x = $data['POINTX'];
        $y = $data['POINTY'];
        $ld = $data['LAND'];
        $mons = $data['MONSTER'];
        $ships = (int)$data['SHIP'];
        $level = $data['LEVEL'];

        // 怪獣のレベル設定
        if ($ld == $init->landMonster || $ld == $init->landSleeper) {
            $BHP = $init->monsterBHP[$mons];

            $DHP = ($init->monsterDHP[$mons] > 0) ? Util::random($init->monsterDHP[$mons] - 1) : Util::random($init->monsterDHP[$mons]);
            $level = $BHP + $DHP;
            $level = $mons * 100 + $level;
        }
        // 船舶のレベル設定
        if ($ld == $init->landShip) {
            $level = Util::navyPack((int)$id, $ships, $init->shipHP[$ships], 0, 0);
        }

        // 更新データ設定
        $land[$x][$y] = $ld;
        $landValue[$x][$y] = $level;

        // マップデータ更新
        $hako->writeLand($id, $island);

        // 設定した値を戻す
        $hako->islands[$number] = $island;

        Util::makeTagMessage("地形を変更しました", "success");

        // マップエディタの表示へ
        $this->editMap($hako, $data);
    }
}

class EditMain
{
    public function execute(): void
    {
        $hako = new HakoEdit;
        $cgi = new CgiImitation;
        $cgi->parseInputData();
        $cgi->getCookies();
        if (!$hako->readIslands($cgi)) {
            HTML::header();
            HakoError::noDataFile();
            HTML::footer();
            exit;
        }
        $cgi->setCookies();
        $edit = new Edit;

        switch ($cgi->dataSet['mode']) {
            case 'enter':
                $html = new HtmlTop();
                $html->header();
                $edit->main($hako, $cgi->dataSet);

                break;

            case 'map':
                $html = new HtmlTop();
                $html->header();
                $edit->editMap($hako, $cgi->dataSet);

                break;

            case 'regist':
                $html = new HtmlTop();
                $html->header();
                $edit->register($hako, $cgi->dataSet);

                break;

            default:
                $html = new HtmlTop();
                $html->header();
                $edit->enter();
        }
        $html->footer();
        exit;
    }
}

$start = new EditMain();
$start->execute();
