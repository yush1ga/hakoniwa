<?php
/**
 * 箱庭諸島 S.E - 画面出力用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once HELPER.'/message/error.php';
require_once HELPER.'/message/success.php';
require_once APP.'/model/hako-log.php';

class HTML
{
    public static function header(): void
    {
        global $init;
        require_once VIEWS.'/header.php';
        require_once VIEWS.'/body.php';
    }

    public static function head(): void
    {
        global $init;
        require_once VIEWS.'/header.php';
    }


    /**
     * HTML <footer />
     * @return void
     */
    public static function footer(): void
    {
        global $init;
        require_once VIEWS.'/footer.php';
    }

    /**
     * 最終更新時刻 ＋ 次ターン更新予定時刻出力
     * @param  [type] $hako [description]
     * @return void
     */
    public function lastModified($hako): void
    {
        global $init;
        require_once VIEWS.'/lastModified.php';
    }

    /**
     * "Y年n月j日 G時i分s秒"形式の文字列を返す
     * @param  integer $t Unixタイムスタンプ
     * @return string
     */
    public function timeToString(int $t): string
    {
        return date("Y年n月j日 G時i分s秒", $t);
    }

    public function pageTitle(string $title, string $subtitle = ''): void
    {
        if ($subtitle=='') {
            echo '<h1 class="title">', $title, '</h1>', "\n";
        } else {
            echo '<h1 class="title">', $title, ' <small>', $subtitle, '</small></h1>', "\n";
        }
    }

    public function print_islandInfoTable($info, int $rank): void
    {
        global $init;

        $rank       = $info['isBF'] ? '<i class="glyphicon glyphicon-asterisk" aria-hidden="true"></i>' : $rank;
        $population = $info['pop'];
        $id         = $info['id'];
        $area       = $info['area'];
        $point      = $info['point'];
        $satelites  = $info['eisei'];
        $zins       = $info['zin'];
        $items      = $info['item'];
        $money      = Util::aboutMoney($info['money']);
        $lottery    = $info['lot'];
        $food       = $info['food'] . $init->unitFood;
        $unemployed = $this->calculations('unemployed', $info);
        // $unemployed = '<span style="color:' .(($unemployed<0)? '#000': '#c7243a'). ';">'. sprintf("%-3d%%", $unemployed). '</span>';
        $farm       = max($info['farm'] * 10, 0);
        $factory    = max($info['factory'] * 10, 0);
        $commerce   = max($info['commerce'] * 10, 0);
        $mountain   = max($info['mountain'] * 10, 0);
        $hatuden    = max($info['hatuden'] * 10, 0);
        $taiji      = $info['taiji'];
        $peop       = sprintf('%+d', $info['peop']).$init->unitPop;
        $okane      = sprintf('%+d', $info['gold']).$init->unitMoney;
        $gohan      = sprintf('%+d', $info['rice']).$init->unitFood;
        $poin       = sprintf('%+d', $info['pots']).'pts';
        $tenki      = $info['tenki'];
        $team       = $info['team'];
        $sport      = [
            'matches'     => $info['shiai'],
            'won'         => $info['kachi'],
            'lose'        => $info['make'],
            'draw'        => $info['hikiwake'],
            'attack'      => $info['kougeki'],
            'defence'     => $info['bougyo'],
            'got_point'   => $info['tokuten'],
            'lose_point'  => $info['shitten']
        ];
        $shiai         = $info['shiai'];
        $kachi         = $info['kachi'];
        $make          = $info['make'];
        $hikiwake      = $info['hikiwake'];
        $kougeki       = $info['kougeki'];
        $bougyo        = $info['bougyo'];
        $tokuten       = $info['tokuten'];
        $shitten       = $info['shitten'];
        $comment       = $info['comment'];
        $keep = '';

        $monster       = ($info['monster'] > 0)? '<strong class="monster">[怪獣'.$info['monster'].'体出現中]</strong>' :'';

        if ($info['keep'] == 1) {
            $comment = '<span class="attention">この島は管理人預かり中です</span>';
            $keep = '<span style="font-size:1.4em;color:#4f4dff;font-weight:700;" title="管理人預かり中">❄</span>';
        }

        $name = Util::islandName($info, $hako->ally, $hako->idToAllyNumber);
        $name = $info['absent'] == 0 ? '<span class="islName">'.$name.'</span>' : '<span class="islName2">'.$name.'('.$info['absent'].')'.'</span>';

        $owner = !empty($info['owner']) ? $info['owner']: 'annonymous';

        $prize = $hako->getPrizeList($info['prize']);

        $point = $info['point'];

        $sora_ = ['', '晴れ☀', '曇り☁', '雨☂', '雷⛈', '雪☃'];
        $sora = "<img src=\"{$init->imgDir}/tenki{$tenki}.gif\" alt=\"{$sora_[$tenki]}\"". ' width="19" height="19">';

        $str_satelites = "";
        for ($i = 0; $i < $init->EiseiNumber; $i++) {
            if (isset($satelites[$i]) && $satelites[$i] > 0) {
                $str_satelites .= "<img src=\"{$init->imgDir}/eisei{$i}.gif\" alt=\"{$init->EiseiName[$i]} {$eisei[$i]}%\" title=\"{$init->EiseiName[$i]} {$eisei[$i]}%\"> ";
            }
        }

        $str_zins = "";
        for ($i = 0; $i < $init->ZinNumber; $i++) {
            if (isset($zins[$i]) && $zins[$i] > 0) {
                $str_zins .= "<img src=\"{$init->imgDir}/zin{$i}.gif\" alt=\"{$init->ZinName[$i]}\" title=\"{$init->ZinName[$i]}\"> ";
            }
        }

        $str_items = "";
        for ($i = 0; $i < $init->ItemNumber; $i++) {
            if (isset($items[$i]) && $items[$i] > 0) {
                if ($i == 20) {
                    $str_items .= "<img src=\"{$init->imgDir}/item{$i}.gif\" alt=\"{$init->ItemName[$i]} {$items[$i]}{$init->unitTree}\"  title=\"{$init->ItemName[$i]} {$item[$i]}{$init->unitTree}\"> ";
                } else {
                    $str_items .= "<img src=\"{$init->imgDir}/item{$i}.gif\" alt=\"{$init->ItemName[$i]}\" title=\"{$init->ItemName[$i]}\"> ";
                }
            }
        }

        $lots = $lottery > 0 ? ' <img src="'.$init->imgDir.'/lot.gif" alt="くじ'.$lottery.'枚" title="'.$lottery.'枚">' : '';

        $viking = '';
        for ($v = $init->shipKind, $c=count($init->shipName); $v < $c; $v++) {
            if ($island['ship'][$v] > 0) {
                $viking .= " <img src=\"{$init->imgDir}/ship{$v}.gif\" width=\"16\" height=\"16\" alt=\"{$init->shipName[$v]}出現中\" title=\"{$init->shipName[$v]}出現中\">";
            }
        }

        $start = (($hako->islandTurn - $island['starturn']) < $init->noAssist)? " 🔰":"";

        $soccer = ($island['soccer'] > 0)?" <span title=\"総合ポイント：{$team}　{$shiai}戦{$kachi}勝{$make}敗{$hikiwake}分　攻撃力：{$kougeki}　守備力：{$bougyo}　得点：{$tokuten}　失点：{$shitten}\">⚽</span>":"";

        // 電力消費量
        $enesyouhi = round(($island['pop'] / 100) + ($island['factory'] * 2/3) + ($island['commerce'] /3) + ($island['mountain'] /4));
        if ($enesyouhi == 0) {
            $ene = "電力消費なし";
        } elseif ($island['hatuden'] == 0) {
            $ene =  '<span style="color:#c7243a;">0%</span>';
        } else {
            // 電力供給率
            $ene = round($island['hatuden'] / $enesyouhi * 100);
            $ene = ($ene < 100) ? '<span style="color:#c7243a;">'.$ene.'%</span>' : $ene.'%';
        }
        echo <<<END
    <tr>
        <th class="NumberCell number" rowspan=5>$j</th>
        <td class="NameCell" rowspan=4>
            <h3><a href="$this_file?Sight=$id">$name</a>$keep$start</h3>
            <div>$monster $soccer</div>
            <div>$prize $viking</div>
            <div>$zins</div>
        </td>
        <td class="InfoCell">$point</td>
        <td class="InfoCell">$pop</td>
        <td class="InfoCell">$area</td>
        <td class="TenkiCell">$sora</td>
        <td class="InfoCell">$money</td>
        <td class="InfoCell">$food</td>
        <td class="InfoCell">$unemployed</td>
    </tr>
    <tr>
        <th class="TitleCell head">$init->nameFarmSize</th>
        <th class="TitleCell head">$init->nameFactoryScale</th>
        <th class="TitleCell head">$init->nameCommercialScale</th>
        <th class="TitleCell head">$init->nameMineScale</th>
        <th class="TitleCell head">$init->namePowerPlantScale</th>
        <th class="TitleCell head">$init->namePowerSupplyRate</th>
        <th class="TitleCell head">$init->nameSatellite</th>
    </tr>
    <tr>
        <td class="InfoCell">$farm</td>
        <td class="InfoCell">$factory</td>
        <td class="InfoCell">$commerce</td>
        <td class="InfoCell">$mountain</td>
        <td class="InfoCell">$hatuden</td>
        <td class="InfoCell">$ene</td>
        <td class="ItemCell">$eiseis</td>
    </tr>
    <tr>
        <th class="TitleCell head">取得アイテム</th>
        <td class="ItemCell" colspan=6>$items</td>
    </tr>
    <tr>
        <td class="NameCell"><small>前ターン比： $poin / $peop / $okane / $gohan</small></td>
        <td class="CommentCell" colspan=7><span class="head">{$owner}：</span> $comment</td>
    </tr>
END;
    }

    private function calculations(string $cat, array $island) :float
    {
        switch ($cat) {
            case 'unemployed':
                return ($island['pop'] - ($island['farm'] + $island['factory'] + $island['commerce'] + $island['mountain'] + $island['hatuden']) * 10) / $island['pop'] * 100;

            default:
                # code...
                break;
        }
    }
}





class HtmlTop extends HTML
{
    public function main($hako, $data): void
    {
        global $init;
        $this_file = $init->baseDir.'/hako-main.php';
        $allyfile  = $init->baseDir.'/hako-ally.php';

        // 開発モードのラジオボタンのチェックフラグ
        $radio  = '';
        $radio2 = 'checked';
        if (mb_strtolower($data['defaultDevelopeMode'] ?? "") !== 'javascript') {
            $radio  = 'checked';
            $radio2 = '';
        }

        // セットするパスワードのチェック
        $defaultPassword = $data['defaultPassword'] ?? '';

        require_once VIEWS.'Index.php';
    }

    /**
     * 島の一覧表を表示
     * @param  [type] $hako     グローバルデータ
     * @param  [type] $start    [description]
     * @param  [type] $sentinel [description]
     * @return [type]           [description]
     */
    public function islandTable(&$hako, int $start, int $sentinel)
    {
        global $init;
        $this_file = $init->baseDir.'/hako-main.php';

        if ($sentinel == 0) {
            return;
        }

        println('<div class="table-responsive">');
        println('<table class="table table-bordered table-condensed">');

        for ($i = $start; $i < $sentinel; $i++) {
            $island = $hako->islands[$i];
            if ($island['isDead'] ?? false) {
                continue;
            }

            // $island['pop'] = $island['pop'] > 1 ? $island['pop'] : 1;
            $j             = $island['isBF'] ? '-' : $i + 1;
            $id            = $island['id'];
            $pop           = $island['pop'].$init->unitPop;
            $area          = $island['area'].$init->unitArea;
            $point         = $island['point'];
            $eisei         = $island['eisei'];
            $zin           = $island['zin'];
            $item          = $island['item'];
            $money         = Util::aboutMoney((int)$island['money']);
            $lot           = $island['lot'];
            $food          = $island['food'] . $init->unitFood;
            $unemployed    = ($island['pop'] - ($island['farm'] + $island['factory'] + $island['commerce'] + $island['mountain'] + $island['hatuden']) * 10) / $island['pop'] * 100;
            $unemployed    = '<span style="color:' .(($unemployed<0)? '#000': '#c7243a'). ';">'. sprintf("%-3d%%", $unemployed). '</span>';
            $farm          = ($island['farm']     <= 0)? $init->notHave: $island['farm']    *10 . $init->unitPop;
            $factory       = ($island['factory']  <= 0)? $init->notHave: $island['factory'] *10 . $init->unitPop;
            $commerce      = ($island['commerce'] <= 0)? $init->notHave: $island['commerce']*10 . $init->unitPop;
            $mountain      = ($island['mountain'] <= 0)? $init->notHave: $island['mountain']*10 . $init->unitPop;
            $hatuden       = ($island['hatuden']  <= 0)? $init->notHave: $island['hatuden'] *10 . 'kW';
            $taiji         = ($island['taiji']    <= 0)? "0".$init->unitMonster: $island['taiji'].$init->unitMonster;
            $peop          = sprintf('%+d', $island['peop']).$init->unitPop;
            $okane         = sprintf('%+d', $island['gold']).$init->unitMoney;
            $gohan         = sprintf('%+d', $island['rice']).$init->unitFood;
            $poin          = sprintf('%+d', $island['pots']).'pts';
            $tenki         = $island['tenki'];
            $team          = $island['team'];
            $shiai         = $island['shiai'];
            $kachi         = $island['kachi'];
            $make          = $island['make'];
            $hikiwake      = $island['hikiwake'];
            $kougeki       = $island['kougeki'];
            $bougyo        = $island['bougyo'];
            $tokuten       = $island['tokuten'];
            $shitten       = $island['shitten'];
            $comment       = $island['comment'];
            $starturn      = $island['starturn'];
            $keep = '';

            $monster       = ($island['monster'] > 0)? '<strong class="text-danger">[怪獣'.$island['monster'].'体出現中]</strong>' :'';

            if ($island['keep'] == 1) {
                $comment = '<span class="attention">この島は管理人預かり中です</span>';
                $keep = '<span style="font-size:1.4em;color:#4f4dff;font-weight:700;" title="管理人預かり中">❄</span>';
            }

            $name = Util::islandName($island, $hako->ally, $hako->idToAllyNumber);
            $name = $island['absent'] == 0 ? '<span class="islName">'.$name.'</span>' : $init->tagName2_.$name.'('.$island['absent'].')'.$init->_tagName2;

            $owner = (!empty($island['owner']))? $island['owner']: 'annonymous';

            $prize = $hako->getPrizeList($island['prize']);

            // $point = $island['point'];

            $_ = ['', '晴れ☀', '曇り☁', '雨☂', '雷⛈', '雪☃'];
            $sora  = "<img src=\"{$init->imgDir}/tenki{$tenki}.gif\" alt=\"{$_[$tenki]}\" title=\"{$_[$tenki]}\"". ' width="19" height="19">';

            $eiseis = "";
            for ($e = 0; $e < $init->EiseiNumber; $e++) {
                if (isset($eisei[$e]) && $eisei[$e] > 0) {
                    $_ = $init->EiseiName[$e].' '.$eisei[$e].'%';
                    $eiseis .= " <img src=\"{$init->imgDir}/eisei{$e}.gif\" alt=\"$_\" title=\"$_\">";
                }
            }

            $zins = "";
            for ($z = 0; $z < $init->ZinNumber; $z++) {
                if (isset($zin[$z]) && $zin[$z] > 0) {
                    $zins .= "<img src=\"{$init->imgDir}/zin{$z}.gif\" alt=\"{$init->ZinName[$z]}\" title=\"{$init->ZinName[$z]}\"> ";
                }
            }

            $items = "";
            for ($t = 0; $t < $init->ItemNumber; $t++) {
                if (isset($item[$t]) && $item[$t] > 0) {
                    if ($t == 20) {
                        $_ = $init->ItemName[$t].' '.$item[$t].$init->unitTree;
                    } else {
                        $_ = $init->ItemName[$t];
                    }
                    $items .= " <img src=\"{$init->imgDir}/item{$t}.gif\" alt=\"$_\" title=\"$_\">";
                }
            }

            $lots = ($lot > 0)? ' <img src="'.$init->imgDir.'/lot.gif" alt="くじ：'.$lot.'枚" title="'.$lot.'枚">':'';

            $viking = "";
            for ($v = $init->shipKind, $c=count($init->shipName); $v < $c; $v++) {
                if ($island['ship'][$v] > 0) {
                    $viking .= " <img src=\"{$init->imgDir}/ship{$v}.gif\" width=\"16\" height=\"16\" alt=\"{$init->shipName[$v]}出現中\" title=\"{$init->shipName[$v]}出現中\">";
                }
            }

            $start = (($hako->islandTurn - $island['starturn']) < $init->noAssist) ? '<sup title="開始ターン：'.$island['starturn'].'">🔰</sup>':'';

            $soccer = ($island['soccer'] > 0)?" <span title=\"総合ポイント：{$team}　{$shiai}戦{$kachi}勝{$make}敗{$hikiwake}分　攻撃力：{$kougeki}　守備力：{$bougyo}　得点：{$tokuten}　失点：{$shitten}\">⚽</span>":"";

            // 電力消費量
            $enesyouhi = round(($island['pop'] / 100) + ($island['factory'] * 2/3) + ($island['commerce'] /3) + ($island['mountain'] /4));
            if ($enesyouhi == 0) {
                $ene = "電力消費なし";
            } elseif ($island['hatuden'] == 0) {
                $ene =  '<span style="color:#c7243a;">0%</span>';
            } else {
                // 電力供給率
                $ene = round($island['hatuden'] / $enesyouhi * 100);
                $ene = ($ene < 100) ? '<span style="color:#c7243a;">'.$ene.'%</span>' : $ene.'%';
            }
            echo <<<END
	<tr>
		<th class="TitleCell head">$init->nameRank</th>
		<th class="TitleCell head">$init->nameSuffix</th>
		<th class="TitleCell head">得点</th>
		<th class="TitleCell head">$init->namePopulation</th>
		<th class="TitleCell head">$init->nameArea</th>
		<th class="TitleCell head">$init->nameWeather</th>
		<th class="TitleCell head">$init->nameFunds $lots</th>
		<th class="TitleCell head">$init->nameFood</th>
		<th class="TitleCell head">$init->nameUnemploymentRate</th>
	</tr>
	<tr>
		<th class="NumberCell number" rowspan=5>$j</th>
		<td class="NameCell" rowspan=4>
			<h3><a href="$this_file?Sight=$id">$name</a>$keep$start</h3>
            <div>$monster $soccer</div>
			<div>$prize $viking</div>
            <div>$zins</div>
		</td>
		<td class="InfoCell">$point</td>
		<td class="InfoCell">$pop</td>
		<td class="InfoCell">$area</td>
		<td class="TenkiCell">$sora</td>
		<td class="InfoCell">$money</td>
		<td class="InfoCell">$food</td>
		<td class="InfoCell">$unemployed</td>
	</tr>
	<tr>
		<th class="TitleCell head">$init->nameFarmSize</th>
		<th class="TitleCell head">$init->nameFactoryScale</th>
		<th class="TitleCell head">$init->nameCommercialScale</th>
		<th class="TitleCell head">$init->nameMineScale</th>
		<th class="TitleCell head">$init->namePowerPlantScale</th>
		<th class="TitleCell head">$init->namePowerSupplyRate</th>
		<th class="TitleCell head">$init->nameSatellite</th>
	</tr>
	<tr>
		<td class="InfoCell">$farm</td>
		<td class="InfoCell">$factory</td>
		<td class="InfoCell">$commerce</td>
		<td class="InfoCell">$mountain</td>
		<td class="InfoCell">$hatuden</td>
		<td class="InfoCell">$ene</td>
		<td class="ItemCell">$eiseis</td>
	</tr>
	<tr>
		<th class="TitleCell head">取得アイテム</th>
		<td class="ItemCell" colspan=6>$items</td>
	</tr>
	<tr>
        <td class="NameCell"><small>前ターン比： $poin / $peop / $okane / $gohan</small></td>
		<td class="CommentCell" colspan=7><span class="head">{$owner}：</span> $comment</td>
	</tr>
END;
        }
        println('</table></div>');
    }

    /**
     * 島の登録と設定
     * @param type $hako
     * @param type $data
     */
    public function register(&$hako, $data = ''): void
    {
        global $init;

        require_once VIEWS.'/conf/register.php';
    }

    /**
     * 新しい島を探す
     * @param  [type] $number [description]
     * @return [type]         [description]
     */
    public function discovery($number)
    {
        global $init;
        $this_file = $init->baseDir . '/hako-main.php';

        require_once VIEWS.'/conf/discovery.php';
    }

    /**
     * 島の名前とパスワードの変更
     */
    public function changeIslandInfo($islandList = ""): void
    {
        global $init;
        $this_file = $init->baseDir . "/hako-main.php";

        require_once VIEWS.'/conf/change/island-info.php';
    }

    /**
     * オーナー名の変更
     */
    public function changeOwnerName($islandList = ""): void
    {
        global $init;
        $this_file = $init->baseDir . "/hako-main.php";

        require_once VIEWS.'/conf/change/owner-name.php';
    }

    /**
     * 最近の出来事
     */
    public function log(): void
    {
        global $init;
        require_once VIEWS.'/log/recent.php';
    }
}


class HtmlMap extends HTML
{
    /**
     * 開発画面
     * @param  [type] $hako [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function owner($hako, $data)
    {
        global $init;
        $this_file = $init->baseDir . "/hako-main.php";

        $id = $data['ISLANDID'];
        $number = $hako->idToNumber[$id];
        $island = $hako->islands[$number];

        // パスワードチェック
        if (!Util::checkPassword($island['password'], $data['PASSWORD'])) {
            HakoError::wrongPassword();

            return;
        }

        // IP情報取得
        $logfile = $init->dirName.'/'.$init->logname;
        $log = file($logfile);
        $fp = fopen($logfile, "w");
        $timedata = date("Y年m月d日(D) H時i分s秒");
        $islandID = $data['ISLANDID'];
        $name = $island['name'].$init->nameSuffix;
        $ip = getenv('REMOTE_ADDR', true);
        if ($ip) {
            $host = gethostbyaddr($ip);
        } else {
            $ip = '192.0.2.0';
            $host = 'unknown';
        }

        // ファイル頭に追記して最大容量超過分を切り捨て
        fwrite($fp, $timedata.','.$islandID.','.$name.','.$ip.','.$host.PHP_EOL);
        for ($i=0,$ax=$init->axesmax-1; $i<$ax; $i++) {
            if (isset($log[$i])) {
                fwrite($fp, $log[$i]);
            }
        }
        fclose($fp);

        // 開発画面
        $this->tempOwer($hako, $data, $number);

        // 島の近況
        $this->islandRecent($island, 1);
    }

    /**
     * 観光画面
     * @param  [type] $hako [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function visitor($hako, $data)
    {
        global $init;
        $this_file = $init->baseDir.'/hako-main.php';

        // idから島番号を取得
        $id = $data['ISLANDID'];
        $number = $hako->idToNumber[$id] ?? -1;

        // なぜかその島がない場合
        if ($number < 0 || $number > $hako->islandNumber) {
            HakoError::problem();

            return;
        }
        // 島の名前を取得
        $island = $hako->islands[$number];
        $name = Util::islandName($island, $hako->ally, $hako->idToAllyNumber);

        // 読み込み
        require_once VIEWS.'/map/main.php';
    }

    //---------------------------------------------------
    // 島の情報
    //---------------------------------------------------
    public function islandInfo($island, $number = 0, $mode = 0): void
    {
        global $init;
        $island['pop'] = max(1, $island['pop']);

        $rank       = $island['isBF'] ? '★' : $number + 1;
        $pop        = $island['pop'].$init->unitPop;
        $area       = $island['area'].$init->unitArea;
        $eisei      = $island['eisei'] ?? "";
        $zin        = $island['zin'] ?? "";
        $item       = $island['item'] ?? "";
        $money      = ($mode == 0) ? Util::aboutMoney($island['money']) : $island['money'].$init->unitMoney;
        $lot        = $island['lot'] ?? "";
        $food       = $island['food'].$init->unitFood;
        $unemployed = ($island['pop'] - ($island['farm'] + $island['factory'] + $island['commerce'] + $island['mountain'] + $island['hatuden']) * 10) / $island['pop'] * 100;
        $unemployed = '<font color="' . ($unemployed < 0 ? 'black' : '#C7243A') . '">' . sprintf("%-3d%%", $unemployed) . '</font>';
        $farm       = ($island['farm'] <= 0) ? $init->notHave : $island['farm'] * 10 . $init->unitPop;
        $factory    = ($island['factory'] <= 0) ? $init->notHave : $island['factory'] * 10 . $init->unitPop;
        $commerce   = ($island['commerce'] <= 0) ? $init->notHave : $island['commerce'] * 10 . $init->unitPop;
        $mountain   = ($island['mountain'] <= 0) ? $init->notHave : $island['mountain'] * 10 . $init->unitPop;
        $hatuden    = ($island['hatuden'] <= 0) ? $init->notHave : $island['hatuden'] * 10 . $init->unitPop;
        $taiji      = (($island['taiji'] <= 0)? 0 : $island['taiji']).$init->unitMonster;
        $tenki      = $island['tenki'];
        $team       = $island['team'];
        $shiai      = $island['shiai'];
        $kachi      = $island['kachi'];
        $make       = $island['make'];
        $hikiwake   = $island['hikiwake'];
        $kougeki    = $island['kougeki'];
        $bougyo     = $island['bougyo'];
        $tokuten    = $island['tokuten'];
        $shitten    = $island['shitten'];
        $comment    = $island['comment'];

        if ($island['keep'] == 1) {
            $comment = '<span class="attention">この島は管理人預かり中です。</span>';
        }

        $_sora = ['','晴れ','曇り','雨','雷','雪'];
        $sora = '<img src="'.$init->imgDir.'/tenki'.$tenki.'.gif" alt="'.$_sora[$tenki].'" title="'.$_sora[$tenki].'" width="19" height="19">';

        $eiseis = "";
        for ($e = 0; $e < $init->EiseiNumber; $e++) {
            $eiseip = "";
            if (isset($eisei[$e])) {
                if ($eisei[$e] > 0) {
                    $eiseip .= $eisei[$e];
                    $eiseis .= "<img src=\"{$init->imgDir}/eisei{$e}.gif\" alt=\"{$init->EiseiName[$e]} {$eiseip}%\" title=\"{$init->EiseiName[$e]} {$eiseip}%\"> ({$eiseip}%)";
                } else {
                    $eiseis .= "";
                }
            }
        }

        $zins = "";
        for ($z = 0; $z < $init->ZinNumber; $z++) {
            if (isset($zin[$z])) {
                if ($zin[$z] > 0) {
                    $zins .= "<img src=\"{$init->imgDir}/zin{$z}.gif\" alt=\"{$init->ZinName[$z]}\" title=\"{$init->ZinName[$z]}\"> ";
                }
            }
        }

        $items = "";
        for ($t = 0; $t < $init->ItemNumber; $t++) {
            if (isset($item[$t])) {
                if ($item[$t] > 0) {
                    if ($t == 20) {
                        $items .= "<img src=\"{$init->imgDir}/item{$t}.gif\" alt=\"{$init->ItemName[$t]} {$item[$t]}{$init->unitTree}\" title=\"{$init->ItemName[$t]} {$item[$t]}{$init->unitTree}\"> ";
                    } else {
                        $items .= "<img src=\"{$init->imgDir}/item{$t}.gif\" alt=\"{$init->ItemName[$t]}\" title=\"{$init->ItemName[$t]}\"> ";
                    }
                }
            }
        }
        $lots = "";
        if ($lot > 0) {
            $lots .= " <img src=\"{$init->imgDir}/lot.gif\" alt=\"{$lot}枚\" title=\"{$lot}枚\">";
        }

        $arm = ($mode == 1) ? 'Lv.'.$island['rena'] : "機密事項";

        // 電力消費量
        $enesyouhi = round($island['pop'] / 100 + $island['factory'] * 2/3 + $island['commerce'] * 1/3 + $island['mountain'] * 1/4);
        if ($enesyouhi == 0) {
            $ene = "電力消費なし";
        } elseif ($island['hatuden'] == 0) {
            $ene =  "<font color=\"#C7243A\">0%</font>";
        } else {
            // 電力供給率
            $ene = round($island['hatuden'] / $enesyouhi * 100);
            if ($ene < 100) {
                // 供給電力不足
                $ene = "<font color=\"#C7243A\">{$ene}%</font>";
            } else {
                // 供給電力充分
                $ene = "{$ene}%";
            }
        }

        // 島の情報
        require_once VIEWS.'/map/island-info.php';
    }

    /**
     * 地形出力
     * @param  [type]  $hako   [description]
     * @param  [type]  $island [description]
     * @param  int     $mode   ミサイル基地等の機密情報表示有無(1/0) // [TODO]: boolにする
     * @return [type]          [description]
     */
    public function islandMap($hako, $island, $mode = 0)
    {
        global $init;

        $land = $island['land'];
        $landValue = $island['landValue'];
        $command = $island['command'];
        $comStr = [];

        // 増減情報
        $peop  = "";
        $okane = "";
        $gohan = "";
        $poin  = "";

        if (isset($island['peop'])) {
            $peop = sprintf("%+d", $island['peop']) . $init->unitPop;
        }
        if (isset($island['gold'])) {
            $okane = sprintf("%+d", $island['gold']) . $init->unitMoney;
        }
        if (isset($island['rice'])) {
            $gohan = sprintf("%+d", $island['rice']) . $init->unitFood;
        }
        if (isset($island['pots'])) {
            $poin = sprintf("%+d", $island['pots']) . 'pts';
        }

        if ($mode == 1) {
            for ($i = 0; $i < $init->commandMax; $i++) {
                $j = $i + 1;
                $com = $command[$i];
                if ($com['kind'] < 51) {
                    if (isset($comStr[$com['x']][$com['y']])) {
                        $comStr[$com['x']][$com['y']] .= "[{$j}]{$init->comName[$com['kind']]} ";
                    }
                }
            }
        }

        require_once VIEWS.'/map/development/map.php';

        println('<p class="text-center">開始ターン：', $island['starturn'], '</p>');

        if (isset($island['soccer']) && $island['soccer'] > 0) {
            //サッカースコアもレスポンシブ対応
            echo <<<END
<div class="table-responsive">
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th class="TitleCell head">総合得点</th>
                <th class="TitleCell head">成績</th>
                <th class="TitleCell head">攻撃力</th>
                <th class="TitleCell head">守備力</th>
                <th class="TitleCell head">得点</th>
                <th class="TitleCell head">失点</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="InfoCell">{$island['team']}</td>
                <td class="InfoCell">{$island['shiai']}戦{$island['kachi']}勝{$island['make']}敗{$island['hikiwake']}分</td>
                <td class="InfoCell">{$island['kougeki']}</td>
                <td class="InfoCell">{$island['bougyo']}</td>
                <td class="InfoCell">{$island['tokuten']}</td>
                <td class="InfoCell">{$island['shitten']}</td>
            </tr>
        </tbody>
    </table>
</div>
END;
        }
    }


    /**
     * 島の近況
     * @param  [type]  $island [description]
     * @param  int     $mode   [description]
     * @return [type]          [description]
     */
    public function islandRecent($island, int $mode = 0)
    {
        global $init;

        echo <<<END
<hr>
<div id="RecentlyLog">
<h2>{$island['name']}{$init->nameSuffix}の近況</h2>
END;

        $log = new Log;
        for ($i = 0; $i < $init->logMax; $i++) {
            $log->logFilePrint($i, $island['id'], $mode);
        }
        println('</div>');
    }

    //---------------------------------------------------
    // 開発画面
    //---------------------------------------------------
    public function tempOwer($hako, $data, $number = 0): void
    {
        global $init;
        $this_file = $init->baseDir.'/hako-main.php';

        $island = $hako->islands[$number];
        $name   = Util::islandName($island, $hako->ally, $hako->idToAllyNumber);
        $width  = $init->islandSize * 32 + 50;
        $height = $init->islandSize * 32 + 100;
        $defaultTarget = $init->targetIsland == 1 ? $island['id'] : $hako->defaultTarget;

        require_once VIEWS.'/map/development/basic.php';
    }

    //---------------------------------------------------
    // 入力済みコマンド表示
    //---------------------------------------------------
    public function tempCommand($number, $command, $hako): void
    {
        global $init;

        $kind = $command['kind'];
        $target = $command['target'];
        $x = $command['x'];
        $y = $command['y'];
        $arg = $command['arg'];
        $comName = "{$init->tagComName_}{$init->comName[$kind]}{$init->_tagComName}";
        $point = "{$init->tagName_}({$x},{$y}){$init->_tagName}";

        $target = !empty($hako->idToName[$target] ?? '') ? $hako->idToName[$target] : '無人';
        $target = '<span class="islName">' . $target . $init->nameSuffix . '</span>';

        $value = $arg * $init->comCost[$kind];
        if ($value === 0) {
            $value = $init->comCost[$kind];
        }
        if ($value < 0) {
            $value = -$value;
            $value .= $kind == $init->comSellTree ? $init->unitTree : $init->unitFood;
        } elseif ($kind == $init->comHikidasi) {
            $value *= 10;
            $value = $value . $init->unitMoney . ' or '. $value . $init->unitFood;
        } else {
            $value .= $init->unitMoney;
        }
        $value = '<span class="islName">' . $value . '</span>';
        $j = sprintf("%02d：", $number + 1);
        echo '<a href="#noop" onclick="ns(' . $number . ');return !1;"><span class="number">' . $j . '</span>';

        switch ($kind) {
            case $init->comMissileSM:
            case $init->comDoNothing:
            case $init->comGiveup:
                $str = $comName;

                break;

            case $init->comMissileNM:
            case $init->comMissilePP:
            case $init->comMissileST:
            case $init->comMissileBT:
            case $init->comMissileSP:
            case $init->comMissileLD:
            case $init->comMissileLU:
                // ミサイル系
                $n = $arg == 0 ? '無制限' : ($arg . '発');
                $str = "$target{$point}へ$comName（<span class=\"islName\">$n</span>）";

                break;

            case $init->comEisei:
                // 人工衛星発射
                if ($arg >= $init->EiseiNumber) {
                    $arg = 0;
                }
                $str = "{$init->tagComName_}{$init->EiseiName[$arg]}打ち上げ{$init->_tagComName}";

                break;

            case $init->comEiseimente:
                // 人工衛星修復
                if ($arg >= $init->EiseiNumber) {
                    $arg = 0;
                }
                $str = "{$init->tagComName_}{$init->EiseiName[$arg]}修復{$init->_tagComName}";

                break;

            case $init->comEiseiAtt:
                // 人工衛星破壊砲
                if ($arg >= $init->EiseiNumber) {
                    $arg = 0;
                }
                $str = "{$target}へ{$init->tagComName_}{$init->EiseiName[$arg]}破壊砲発射{$init->_tagComName}";

                break;

            case $init->comEiseiLzr:
                // 衛星レーザー
                $str = "{$target}{$point}へ{$comName}";

                break;

            case $init->comSendMonster:
            case $init->comSendSleeper:
                // 怪獣派遣
                $str = "{$target}へ{$comName}";

                break;

            case $init->comSell:
            case $init->comSellTree:
                // 食料・木材輸出
                $str ="{$comName}{$value}";

                break;

            case $init->comMoney:
            case $init->comFood:
                // 援助
                $str = "{$target}へ{$comName}{$value}";

                break;

            case $init->comDestroy:
                // 掘削
                if ($arg != 0) {
                    $str = "{$point}で{$comName}（予算{$value}）";
                } else {
                    $str = "{$point}で{$comName}";
                }

                break;

            case $init->comLot:
                // 宝くじ購入
                if ($arg == 0) {
                    $arg = 1;
                } elseif ($arg > 30) {
                    $arg = 30;
                }
                $str = "{$comName}（予算{$value}）";

                break;

            case $init->comDbase:
                // 防衛施設
                if ($arg == 0) {
                    $arg = 1;
                } elseif ($arg > $init->dBaseHP) {
                    $arg = $init->dBaseHP;
                }
                $str = "{$point}で{$comName}（耐久力{$arg}）";

                break;

            case $init->comSdbase:
                // 海底防衛施設
                if ($arg == 0) {
                    $arg = 1;
                } elseif ($arg > $init->sdBaseHP) {
                    $arg = $init->sdBaseHP;
                }
                $str = "{$point}で{$comName}（耐久力{$arg}）";

                break;

            case $init->comSoukoM:
                $flagm = 1;
                // no break
            case $init->comSoukoF:
                // 倉庫建設
                if ($arg == 0) {
                    $str = "{$point}で{$comName}（セキュリティ強化）";
                } else {
                    if ($flagm == 1) {
                        $str = "{$point}で{$comName}（{$value}）";
                    } else {
                        $str = "{$point}で{$comName}（{$value}）";
                    }
                }

                break;

            case $init->comHikidasi:
                // 倉庫引き出し
                if ($arg == 0) {
                    $arg = 1;
                }
                $str = "{$comName}（{$value}）";

                break;

            case $init->comMakeShip:
                // 造船
                if ($arg >= $init->shipKind) {
                    $arg = $init->shipKind - 1;
                }
                $str = "{$point}で{$comName}（{$init->shipName[$arg]}）";

                break;

            case $init->comShipBack:
                // 船の破棄
                $str = "{$point}で{$comName}";

                break;

            case $init->comFarm:
            case $init->comSfarm:
            case $init->comNursery:
            case $init->comFactory:
            case $init->comCommerce:
            case $init->comMountain:
            case $init->comHatuden:
            case $init->comBoku:
                // 回数付き
                $str = "{$point}で{$comName}";
                $str .= ($arg != 0) ?: "（{$arg}回）";

                break;

            case $init->comPropaganda:
            case $init->comOffense:
            case $init->comDefense:
            case $init->comPractice:
                // 強化
                $str = "{$comName}（{$arg}回）";

                break;

            case $init->comPlaygame:
                // 試合
                $str = "{$target}と{$comName}";

                break;

            case $init->comSendShip:
                // 船派遣
                $str = "{$target}へ{$point}の{$comName}";

                break;

            case $init->comReturnShip:
                // 船帰還
                $str = "{$target}{$point}の{$comName}";

                break;

            default:
                // 座標付き
                $str = "{$point}で{$comName}";
        }
        echo "$str</a><br>";
    }
    //---------------------------------------------------
    // 新しく発見した島
    //---------------------------------------------------
    public function newIslandHead($name): void
    {
        global $init;

        println('<h1 class="text-center">', $init->nameSuffix, 'を発見しました！</h1>');
        println('<p class="lead"><span class="big islName">「', $name, $init->nameSuffix, '」</span>と命名しました。</p>');
    }

    //---------------------------------------------------
    // 目標捕捉モード
    //---------------------------------------------------
    public function printTarget($hako, $data): void
    {
        global $init;

        // idから島番号を取得
        $id = $data['ISLANDID'];
        $number = $hako->idToNumber[$id];
        // なぜかその島がない場合
        if ($number < 0 || $number > $hako->islandNumber) {
            HakoError::problem();

            return;
        }
        $island = $hako->islands[$number];
        echo <<<END
<script>
function ps(x, y) {
	window.opener.document.forms.InputPlan.POINTX.options[x].selected = true;
	window.opener.document.forms.InputPlan.POINTY.options[y].selected = true;
	return true;
}
</script>

<div class="text-center">
<span class="big islName">{$island['name']}$init->nameSuffix</span>
</div>
END;
        //島の地図
        $this->islandMap($hako, $island, 2);
    }
}


class HtmlMapJS extends HtmlMap
{

    //---------------------------------------------------
    // 開発画面
    //---------------------------------------------------
    public function tempOwer($hako, $data, $number = 0): void
    {
        global $init;
        $this_file = $init->baseDir . "/hako-main.php";

        $island = $hako->islands[$number];
        $name = Util::islandName($island, $hako->ally, $hako->idToAllyNumber);
        $width = $init->islandSize * 32 + 50;
        $height = $init->islandSize * 32 + 100;

        // コマンドセット
        $set_com = "";
        $com_max = "";
        $commandMax = $init->commandMax;
        for ($i = 0; $i < $commandMax; $i++) {
            // 各要素の取り出し
            $command = $island['command'][$i];
            $s_kind = $command['kind'];
            $s_target = $command['target'];
            $s_x = $command['x'];
            $s_y = $command['y'];
            $s_arg = $command['arg'];

            // コマンド登録
            if ($i == $commandMax - 1) {
                $set_com .= "[$s_kind, $s_x, $s_y, $s_arg, $s_target]\n";
                $com_max .= "0";
            } else {
                $set_com .= "[$s_kind, $s_x, $s_y, $s_arg, $s_target],\n";
                $com_max .= "0, ";
            }
        }
        //コマンドリストセット
        $l_kind;
        $set_listcom = "";
        $click_com = ["", "", "", "", "", "", "", ""];
        $All_listCom = 0;
        $com_count = count($init->commandDivido);
        for ($m = 0; $m < $com_count; $m++) {
            [$aa, $dd, $ff] = explode(",", $init->commandDivido[$m]);
            $set_listcom .= "[ ";
            for ($i = 0; $i < $init->commandTotal; $i++) {
                $l_kind = $init->comList[$i];
                $l_cost = $init->comCost[$l_kind];
                if ($l_cost == 0) {
                    $l_cost = '無料';
                } elseif ($l_cost < 0) {
                    $l_cost = - $l_cost;
                    if ($l_kind == 83) {
                        $l_cost .= $init->unitTree;
                    } else {
                        $l_cost .= $init->unitFood;
                    }
                } else {
                    $l_cost .= $init->unitMoney;
                }
                if ($l_kind > $dd-1 && $l_kind < $ff+1) {
                    $set_listcom .= "[$l_kind, '{$init->comName[$l_kind]}', '{$l_cost}'],\n";
                    if ($m >= 0 && $m <= 7) {
                        $click_com[$m] .= "<a href='javascript:void(0);' onclick='cominput(InputPlan, 6, {$l_kind})' onkeypress='cominput(InputPlan, 6, {$l_kind})' style='text-decoration:none'>{$init->comName[$l_kind]}({$l_cost})</a><br>\n";
                    }
                    $All_listCom++;
                }
                //if($l_kind < $ff+1) {
                //	next;
                //}
            }
            $bai = mb_strlen($set_listcom);
            $set_listcom = mb_substr($set_listcom, 0, $bai - 2);
            $set_listcom .= " ],\n";
        }
        $bai = mb_strlen($set_listcom);
        $set_listcom = mb_substr($set_listcom, 0, $bai - 2);
        if (empty($data['defaultKind'])) {
            $default_Kind = 1;
        } else {
            $default_Kind = $data['defaultKind'];
        }
        // 船リストセット
        $set_ships = "";
        for ($i = 0; $i < $init->shipKind; $i++) {
            $set_ships .= "'".$init->shipName[$i]."',";
        }
        // 衛星リストセット
        //$set_eisei = implode("," , $init->EiseiName);
        $set_eisei = "";
        for ($i = 0; $i < count($init->EiseiName); $i++) {
            $set_eisei .= "'".$init->EiseiName[$i]."',";
        }
        $set_eisei = mb_substr($set_eisei, 0, -1);  // ケツカンマを削除

        // 島リストセット
        $set_island = "";
        for ($i = 0; $i < $hako->islandNumber; $i++) {
            $l_name = $hako->islands[$i]['name'];
            $l_name = preg_replace("/'/", "\'", $l_name);
            $l_id = $hako->islands[$i]['id'];
            if ($i == $hako->islandNumber - 1) {
                $set_island .= "[$l_id, '$l_name']\n";
            } else {
                $set_island .= "[$l_id, '$l_name'],\n";
            }
        }
        $set_island = mb_substr($set_island, 0, -1);  // ケツカンマを削除


        $defaultTarget = ($init->targetIsland == 1) ? $island['id'] : $hako->defaultTarget;

        require_once VIEWS.'/map/development/js.php';

        echo <<<END
<script type="text/javascript">
var w;
var p = $defaultTarget;

// ＪＡＶＡスクリプト開発画面配布元
// あっぽー庵箱庭諸島（ http://appoh.execweb.cx/hakoniwa/ ）
// Programmed by Jynichi Sakai（あっぽー）
// ↑ 削除しないで下さい。
var str;
var g  = [$com_max];
var k1 = [$com_max];
var k2 = [$com_max];
var tmpcom1 = [ [0, 0, 0, 0, 0] ];
var tmpcom2 = [ [0, 0, 0, 0, 0] ];
var command = [$set_com];
var comlist = [$set_listcom];

var islname   = [$set_island];
var shiplist  = [$set_ships];
var eiseilist = [$set_eisei];

var mx, my;

function init() {

	for(var i = 0; i < command.length; i++) {
		for(var s = 0; s < $com_count; s++) {
			var comlist2 = comlist[s];
			for(var j = 0; j < comlist2.length; j++) {
				if(command[i][0] == comlist2[j][0]) {
					g[i] = comlist2[j][1];
				}
			}
		}
	}
	SelectList('');
	outp();
	str = plchg();
	str = '<font color="blue">■ 送信済み ■<\\/font><br>' + str;
	disp(str, "");
	document.onmousemove = Mmove;
	// if(document.layers) {
	// 	//document.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
	// 	document.addEventListener("DOMContentLoaded", Event.MOUSEMOVE | Event.MOUSEUP, false);
	// }
	document.onmouseup = Mup;
	document.onmousemove = Mmove;
	document.onkeydown = Kdown;
	document.ch_numForm.AMOUNT.options.length = 100;
	for(i=0;i<document.ch_numForm.AMOUNT.options.length;i++){
		document.ch_numForm.AMOUNT.options[i].value = i;
		document.ch_numForm.AMOUNT.options[i].text = i;
	}
	document.InputPlan.sendProj.disabled = true;
	ns(0);
}

function cominput(theForm, x, k, z) {
	var a = theForm.number.options[theForm.number.selectedIndex].value;
	var b = theForm.commands.options[theForm.commands.selectedIndex].value;
	var c = theForm.POINTX.options[theForm.POINTX.selectedIndex].value;
	var d = theForm.POINTY.options[theForm.POINTY.selectedIndex].value;
	var e = theForm.AMOUNT.options[theForm.AMOUNT.selectedIndex].value;
	var f = theForm.TARGETID.options[theForm.TARGETID.selectedIndex].value;

	if(x == 6){
		b = k; menuclose();
	}

	var newNs = a;
	if (x == 1 || x == 2 || x == 6){
		if(x == 6) {
			b = k;
		}
		if(x != 2) {
			for(var i = $init->commandMax - 1; i > a; i--) {
				command[i] = command[i-1];
				g[i] = g[i-1];
			}
		}
		for(s = 0; s < $com_count ;s++) {
			var comlist2 = comlist[s];
			for(i = 0; i < comlist2.length; i++){
				if(comlist2[i][0] == b){
					g[a] = comlist2[i][1];
					break;
				}
			}
		}
		command[a] = [b,c,d,e,f];
		newNs++;
//		menuclose();

	} else if(x == 3) {
		var num = (k) ? k-1 : a;
		for(i = Math.floor(num); i < ($init->commandMax - 1); i++) {
			command[i] = command[i + 1];
			g[i] = g[i+1];
		}
		command[$init->commandMax - 1] = [81, 0, 0, 0, 0];
		g[$init->commandMax - 1] = '資金繰り';

	} else if(x == 4) {
		i = Math.floor(a);
		if (i == 0){ return true; }
		i = Math.floor(a);
		tmpcom1[i] = command[i];tmpcom2[i] = command[i - 1];
		command[i] = tmpcom2[i];command[i-1] = tmpcom1[i];
		k1[i] = g[i];k2[i] = g[i - 1];
		g[i] = k2[i];g[i-1] = k1[i];
		ns(--i);
		str = plchg();
		str = '<font color="#C7243A"><strong>■ 未送信 ■<\\/strong><\\/font><br>' + str;
		disp(str,"white");
		outp();
        //上ボタンの挙動修正
		//newNs = i+1;
		newNs = i;
	} else if(x == 5) {
		i = Math.floor(a);
		if (i == $init->commandMax - 1){ return true; }
		tmpcom1[i] = command[i];tmpcom2[i] = command[i + 1];
		command[i] = tmpcom2[i];command[i + 1] = tmpcom1[i];
		k1[i] = g[i];k2[i] = g[i + 1];
		g[i] = k2[i];g[i + 1] = k1[i];
		newNs = i+1;
	}else if(x == 7){
		// 移動
		var ctmp = command[k];
		var gtmp = g[k];
		if(z > k) {
			// 上から下へ
			for(i = k; i < z-1; i++) {
				command[i] = command[i+1];
				g[i] = g[i+1];
			}
		} else {
			// 下から上へ
			for(i = k; i > z; i--) {
				command[i] = command[i-1];
				g[i] = g[i-1];
			}
		}
		command[i] = ctmp;
		g[i] = gtmp;
		newNs = i+1;
	}else if(x == 8){
		command[a][3] = k;
	}
	str = plchg();
	str = '<font color="#C7243A"><b>■ 未送信 ■<\\/b><\\/font><br>' + str;
	disp(str, "");
	outp();
	theForm.sendProj.disabled = false;
	ns(newNs);

	return true;
}

function plchg() {
	var strn1 = "";
	var strn2 = "";
	var arg = "";
	for(var i = 0; i < $init->commandMax; i++) {
		var c = command[i];
		var kind = '{$init->tagComName_}' + g[i] + '{$init->_tagComName}';
		var x = c[1];
		var y = c[2];
		var tgt = c[4];
		var point = '{$init->tagName_}' + "(" + x + "," + y + ")" + '{$init->_tagName}';

		for(var j = 0; j < islname.length ; j++) {
			if(tgt == islname[j][0]){
				tgt = '{$init->tagName_}' + islname[j][1] + "島" + '{$init->_tagName}';
			}
		}

		if(c[0] == $init->comMissileSM || c[0] == $init->comDoNothing || c[0] == $init->comGiveup){
			// ミサイル撃ち止め、資金繰り、島の放棄
			strn2 = kind;
		}else if(c[0] == $init->comMissileNM || // ミサイル関連
			c[0] == $init->comMissilePP ||
			c[0] == $init->comMissileST ||
			c[0] == $init->comMissileBT ||
			c[0] == $init->comMissileSP ||
			c[0] == $init->comMissileLD ||
			c[0] == $init->comMissileLU){
			if(c[3] == 0) {
				arg = "（無制限）";
			} else {
				arg = "（" + c[3] + "発）";
			}
			strn2 = tgt + point + "へ" + kind + arg;
		} else if((c[0] == $init->comSendMonster) || (c[0] == $init->comSendSleeper)) { // 怪獣派遣
			strn2 = tgt + "へ" + kind;
		} else if(c[0] == $init->comSell) { // 食料輸出
			if(c[3] == 0){ c[3] = 1; }
			arg = c[3] * 100;
			arg = "（" + arg + "{$init->unitFood}）";
			strn2 = kind + arg;
		} else if(c[0] == $init->comSellTree) { // 木材輸出
			if(c[3] == 0){ c[3] = 1; }
			arg = c[3] * 10;
			arg = "（" + arg + "{$init->unitTree}）";
			strn2 = kind + arg;
		} else if(c[0] == $init->comMoney) { // 資金援助
			if(c[3] == 0){ c[3] = 1; }
			arg = c[3] * {$init->comCost[$init->comMoney]};
			arg = "（" + arg + "{$init->unitMoney}）";
			strn2 = tgt + "へ" + kind + arg;
		} else if(c[0] == $init->comFood) { // 食料援助
			if(c[3] == 0){ c[3] = 1; }
			arg = c[3] * 100;
			arg = "（" + arg + "{$init->unitFood}）";
			strn2 = tgt + "へ" + kind + arg;
		} else if(c[0] == $init->comDestroy) { // 掘削
			if(c[3] == 0){
				strn2 = point + "で" + kind;
			} else {
				arg = c[3] * {$init->comCost[$init->comDestroy]};
				arg = "（予算" + arg + "{$init->unitMoney}）";
				strn2 = point + "で" + kind + arg;
			}
		} else if(c[0] == $init->comLot) { // 宝くじ購入
			if(c[3] == 0) c[3] = 1;
			if(c[3] > 30) c[3] = 30;
				arg = c[3] * {$init->comCost[$init->comLot]};
				arg = "（予算" + arg + "{$init->unitMoney}）";
				strn2 = kind + arg;
		} else if(c[0] == $init->comDbase) { // 防衛施設
			if(c[3] == 0) c[3] = 1;
			if(c[3] > $init->dBaseHP) c[3] = $init->dBaseHP;
				arg = c[3];
				arg = "(耐久力" + arg + "）";
				strn2 = point + "で" + kind + arg;
		} else if(c[0] == $init->comSdbase) { // 海底防衛施設
			if(c[3] == 0) c[3] = 1;
			if(c[3] > $init->sdBaseHP) c[3] = $init->sdBaseHP;
				arg = c[3];
				arg = "(耐久力" + arg + "）";
				strn2 = point + "で" + kind + arg;
		} else if(c[0] == $init->comShipBack){ // 船の破棄
				strn2 = point + "で" + kind;
		} else if(c[0] == $init->comSoukoM){ // 倉庫建設(貯金)
			if(c[3] == 0) {
				arg = "(セキュリティ強化)";
				strn2 = point + "で" + kind + arg;
			} else {
				arg = c[3] * 1000;
				arg = "(" + arg + "{$init->unitMoney})";
				strn2 = point + "で" + kind + arg;
			}
		} else if(c[0] == $init->comSoukoF){ // 倉庫建設(貯食)
			if(c[3] == 0) {
				arg = "(セキュリティ強化)";
				strn2 = point + "で" + kind + arg;
			} else {
				arg = c[3] * 1000;
				arg = "(" + arg + "{$init->unitFood})";
				strn2 = point + "で" + kind + arg;
			}
		} else if(c[0] == $init->comHikidasi) { // 倉庫引き出し
			if(c[3] == 0) c[3] = 1;
			arg = c[3] * 1000;
			arg = "（" + arg + "{$init->unitMoney} or " + arg + "{$init->unitFood}）";
			strn2 = point + "で" + kind + arg;
		} else if(c[0] == $init->comFarm || // 農場、海底農場、工場、商業ビル、採掘場整備、発電所、僕の引越し
			c[0] == $init->comSfarm ||
			c[0] == $init->comFactory ||
			c[0] == $init->comCommerce ||
			c[0] == $init->comMountain ||
			c[0] == $init->comHatuden ||
			c[0] == $init->comBoku) {
			if(c[3] != 0){
				arg = "（" + c[3] + "回）";
				strn2 = point + "で" + kind + arg;
			}else{
				strn2 = point + "で" + kind;
			}
		} else if(c[0] == $init->comPropaganda || // 誘致活動
			c[0] == $init->comOffense || // 強化
			c[0] == $init->comDefense ||
			c[0] == $init->comPractice) {
			if(c[3] != 0){
				arg = "（" + c[3] + "回）";
				strn2 = kind + arg;
			}else{
				strn2 = kind;
			}
		} else if(c[0] == $init->comPlaygame) { // 試合
			strn2 = tgt + "と" + kind;
		} else if(c[0] == $init->comMakeShip){ // 造船
			if(c[3] >= $init->shipKind) {
				c[3] = $init->shipKind - 1;
			}
			arg = c[3];
			strn2 = point + "で" + kind + " (" + shiplist[arg] + ")";
		} else if(c[0] == $init->comSendShip){ // 船派遣
			strn2 = tgt + "へ" + point + "の" + kind;
		} else if(c[0] == $init->comReturnShip){ // 船帰還
			strn2 = tgt + point + "の" + kind;
		} else if(c[0] == $init->comEisei){ // 人工衛星打ち上げ
			if(c[3] >= $init->EiseiNumber) {
				c[3] = 0;
			}
			arg = c[3];
			strn2 = '{$init->tagComName_}' + eiseilist[arg] + "打ち上げ" + '{$init->_tagComName}';
		} else if(c[0] == $init->comEiseimente){ // 人工衛星修復
			if(c[3] >= $init->EiseiNumber) {
				c[3] = 0;
			}
			arg = c[3];
			strn2 = '{$init->tagComName_}' + eiseilist[arg] + "修復" + '{$init->_tagComName}';
		} else if(c[0] == $init->comEiseiAtt){ // 人工衛星破壊
			if(c[3] >= $init->EiseiNumber) {
				c[3] = 0;
			}
			arg = c[3];
			strn2 = tgt + "へ" + '{$init->tagComName_}' + eiseilist[arg] + "破壊砲発射" + '{$init->_tagComName}';
		} else if(c[0] == $init->comEiseiLzr) { // 衛星レーザー
			strn2 = tgt + point + "へ" + kind;
		}else{
			strn2 = point + "で" + kind;
		}
		tmpnum = '';
		if(i < 9){ tmpnum = '0'; }
		strn1 +=
			'<div id="com_'+i+'" '+
				'onmouseover="mc_over('+i+');return false;" '+
				'><a HREF="javascript:void(0);" onclick="ns('+i+')" onkeypress="ns('+i+')" '+
				'onmousedown="return comListMove('+i+');" '+'ondblclick="chNum('+c[3]+');return false;" '+
				'><nobr>'+
				tmpnum+(i+1)+':'+
				strn2+'<\\/nobr><\\/a><\\/div>\\n';
	}

	return strn1;
}

function disp(str,bgclr) {
	if(str==null) {
		str = "";
	}
	LayWrite('IsSynced', str);
	SetBG('plan', bgclr);
}

function outp() {
	comary = "";

	for(k = 0; k < command.length; k++){
		comary = comary + command[k][0]
			+ " " + command[k][1]
			+ " " + command[k][2]
			+ " " + command[k][3]
			+ " " + command[k][4]
			+ " " ;
	}
	document.InputPlan.COMARY.value = comary;
}

function ps(x, y) {
	document.InputPlan.POINTX.options[x].selected = true;
	document.InputPlan.POINTY.options[y].selected = true;
	if(!(document.InputPlan.MENUOPEN.checked)) {
		moveLAYER("menu", mx+10, my-50);
	}
	NaviClose();
	return true;
}

function ns(x) {
	if (x == $init->commandMax){
		return true;
	}
	document.InputPlan.number.options[x].selected = true;
	return true;
}

function set_com(x, y, land) {
	com_str = land + " ";
	for(i = 0; i < $init->commandMax; i++) {
		c = command[i];
		x2 = c[1];
		y2 = c[2];
		if(x == x2 && y == y2 && c[0] < 30){
			com_str += "[" + (i + 1) +"]" ;
			kind = g[i];
			if(c[0] == $init->comDestroy){
				if(c[3] == 0){
					com_str += kind;
				} else {
					arg = c[3] * 200;
					arg = "（予算" + arg + "{$init->unitMoney}）";
					com_str += kind + arg;
				}
			} else if(c[0] == $init->comLot){
				if(c[3] == 0) c[3] = 1;
				if(c[3] > 30) c[3] = 30;
					arg = c[3] * 300;
					arg = "（予算" + arg + "{$init->unitMoney}）";
					com_str += kind + arg;
			} else if(c[0] == $init->comFarm ||
				c[0] == $init->comSfarm ||
				c[0] == $init->comFactory ||
				c[0] == $init->comCommerce ||
				c[0] == $init->comMountain ||
				c[0] == $init->comHatuden ||
				c[0] == $init->comBoku ||
				c[0] == $init->comPropaganda ||
				c[0] == $init->comOffense ||
				c[0] == $init->comDefense ||
				c[0] == $init->comPractice) {
				if(c[3] != 0){
					arg = "（" + c[3] + "回）";
					com_str += kind + arg;
				} else {
					com_str += kind;
				}
			} else {
				com_str += kind;
			}
			com_str += " ";
		}
	}
	document.InputPlan.COMSTATUS.value= com_str;
}

function SelectList(theForm) {
	var u, selected_ok;
	if(!theForm) { s = '' }
	else { s = theForm.menu.options[theForm.menu.selectedIndex].value; }
	if(s == ''){
		u = 0; selected_ok = 0;
		document.InputPlan.commands.options.length = $All_listCom;
		for (i=0; i<comlist.length; i++) {
			var command = comlist[i];
			for (a=0; a<command.length; a++) {
				comName = command[a][1] + "(" + command[a][2] + ")";
				document.InputPlan.commands.options[u].value = command[a][0];
				document.InputPlan.commands.options[u].text = comName;
				if(command[a][0] == $default_Kind){
					document.InputPlan.commands.options[u].selected = true;
					selected_ok = 1;
				}
				u++;
			}
		}
		if(selected_ok == 0)
			document.InputPlan.commands.selectedIndex = 0;
	} else {
		var command = comlist[s];
		document.InputPlan.commands.options.length = command.length;
		for (i=0; i<command.length; i++) {
			comName = command[i][1] + "(" + command[i][2] + ")";
			document.InputPlan.commands.options[i].value = command[i][0];
			document.InputPlan.commands.options[i].text = comName;
			if(command[i][0] == $default_Kind){
				document.InputPlan.commands.options[i].selected = true;
				selected_ok = 1;
			}
		}
		if(selected_ok == 0) {
			document.InputPlan.commands.selectedIndex = 0;
		}
	}
}

function moveLAYER(layName,x,y){
	var el = document.getElementById(layName);
	el.style.left = x + "px";
	el.style.top  = y + "px";
}

function menuclose() {
	moveLAYER("menu", -500, -500);
}

function Mmove(e){
	mx = e.pageX;
	my = e.pageY;

	return moveLay.move();
}

function LayWrite(layName, str) {
	document.getElementById(layName).innerHTML = str;
}

function SetBG(layName, bgclr) {
	document.getElementById(layName).style.backgroundColor = bgclr;
}

var oldNum=0;
function selCommand(num) {
	document.getElementById('com_'+oldNum).style.backgroundColor = '';
	document.getElementById('com_'+num).style.backgroundColor = '#FFFFAA';
	oldNum = num;
}

/* コマンド ドラッグ＆ドロップ用追加スクリプト */
var moveLay = new MoveFalse();
var newLnum = -2;
var Mcommand = false;

function Mup() {
	moveLay.up();
	moveLay = new MoveFalse();
}

function setBorder(num, color) {
	if(color.length == 4) {
		document.getElementById('com_'+num).style.borderTop = ' 1px solid '+color;
	} else {
		document.getElementById('com_'+num).style.border = '0px';
	}
}

function mc_out() {
	if(Mcommand && newLnum >= 0) {
		setBorder(newLnum, '');
		newLnum = -1;
	}
}

function mc_over(num) {
	if(Mcommand) {
		if(newLnum >= 0) setBorder(newLnum, '');
		newLnum = num;
		setBorder(newLnum, '#116'); // blue
	}
}

function comListMove(num) {
	moveLay = new MoveComList(num);
	return (document.layers) ? true : false;
}

function MoveFalse() {
	this.move = function() { }
	this.up = function() { }
}

function MoveComList(num) {
	var setLnum = num;
	Mcommand = true;
	LayWrite('mc_div', '<NOBR><strong>'+(num+1)+': '+g[num]+'</strong></NOBR>');
	this.move = function() {
		moveLAYER('mc_div', mx+10, my-30);
		return false;
	}
	this.up = function() {
		if(newLnum >= 0) {
			var com = command[setLnum];
			cominput(document.InputPlan,7,setLnum,newLnum);
		} else if(newLnum == -1) {
			cominput(document.InputPlan,3,setLnum+1);
		}
		mc_out();
		newLnum = -2;
		Mcommand = false;
		moveLAYER("mc_div",-50,-50);
	}
}

function showElement(layName) {
	var element = document.getElementById(layName).style;
	element.display = "block";
	element.visibility ='visible';
}

function hideElement(layName) {
	var element = document.getElementById(layName).style;
	element.display = "none";
}

function chNum(num) {
	document.ch_numForm.AMOUNT.options.length = 100;
	for(var i=0; i<document.ch_numForm.AMOUNT.options.length; i++){
		if(document.ch_numForm.AMOUNT.options[i].value == num){
			document.ch_numForm.AMOUNT.selectedIndex = i;
			document.ch_numForm.AMOUNT.options[i].selected = true;
			moveLAYER('ch_num', mx-10, my-60);
			showElement('ch_num');
			break;
		}
	}
}

function chNumDo() {
	var num = document.ch_numForm.AMOUNT.options[document.ch_numForm.AMOUNT.selectedIndex].value;
	cominput(document.InputPlan,8,num);
	hideElement('ch_num');
}

function Kdown(e){
	var c, el;
	var m = document.InputPlan.AMOUNT.selectedIndex;
	if(m > 9) {
		m = 0;
	}

	if (e.altKey || e.ctrlKey || e.shiftKey) {
		return;
	}
	c = e.which;
	el = new String(e.target.tagName);
	el = el.toUpperCase();
	if (el == "INPUT") {
		return;
	}

	c = String.fromCharCode(c);

	// 押されたキーに応じて計画番号を設定する
	switch (c) {
		case 'A': c = $init->comPrepare; break; // 整地
		case 'J': c = $init->comPrepare2; break; // 地ならし
		case 'U': c = $init->comReclaim; break; // 埋め立て
		case 'K': c = $init->comDestroy; break; // 掘削
		case 'B': c = $init->comSellTree; break; // 伐採
		case 'P': c = $init->comPlant; break; // 植林
		case 'N': c = $init->comFarm; break; // 農場整備
		case 'I': c = $init->comFactory; break; // 工場建設
		case 'S': c = $init->comMountain; break; // 採掘場整備
		case 'D': c = $init->comDbase; break; // 防衛施設建設
		case 'M': c = $init->comBase; break; // ミサイル基地建設
		case 'F': c = $init->comSbase; break; // 海底基地建設
		case '-': c = $init->comDoNothing; break; //INS 資金繰り
		case '.': cominput(InputPlan,3); return; //DEL 削除
		case'\b': //BS 一つ前削除
		var no = document.InputPlan.commands.selectedIndex;
		if(no > 0) {
			document.InputPlan.commands.selectedIndex = no - 1;
		}
		cominput(InputPlan,3);
		return;
		case '0':case'`': document.InputPlan.AMOUNT.selectedIndex = m*10+0; return;
		case '1':case'a': document.InputPlan.AMOUNT.selectedIndex = m*10+1; return;
		case '2':case'b': document.InputPlan.AMOUNT.selectedIndex = m*10+2; return;
		case '3':case'c': document.InputPlan.AMOUNT.selectedIndex = m*10+3; return;
		case '4':case'd': document.InputPlan.AMOUNT.selectedIndex = m*10+4; return;
		case '5':case'e': document.InputPlan.AMOUNT.selectedIndex = m*10+5; return;
		case '6':case'f': document.InputPlan.AMOUNT.selectedIndex = m*10+6; return;
		case '7':case'g': document.InputPlan.AMOUNT.selectedIndex = m*10+7; return;
		case '8':case'h': document.InputPlan.AMOUNT.selectedIndex = m*10+8; return;
		case '9':case'i': document.InputPlan.AMOUNT.selectedIndex = m*10+9; return;
		case 'Z':case'j': document.InputPlan.AMOUNT.selectedIndex = 0; return;
		default:
		// IE ではリロードのための F5 まで拾うので、ここに処理をいれてはいけない
		return;
	}
	cominput(document.InputPlan, 6, c);
}

function settarget(part){
	p = part.options[part.selectedIndex].value;
}

function targetopen() {
	w = window.open("{$this_file}?target=" + p, "","width={$width},height={$height},scrollbars=1,resizable=1,toolbar=1,menubar=1,location=1,directories=0,status=1");
}

</script>
END;
        $this->islandInfo($island, $number, 1);

        echo <<<END
<div id="menu" style="position:absolute; top:-500px;left:-500px; overflow:auto;width:360px;height:350px;">
	<table border=0 class="PopupCell" onClick="menuclose()">
		<tr valign=top>
			<td>
				$click_com[0]
				<hr>
				$click_com[1]
			</td>
			<td>
				$click_com[2]
				<hr>
				$click_com[3]
			</td>
		</tr>
		<tr valign=top>
			<td>
				$click_com[4]
				<hr>
				$click_com[5]
			</td>
			<td>
			$click_com[6]
			</td>
		</tr>
	</table>
</div>

<div ID="mc_div" style="position:absolute;top:-50;left:-50;height:22px;">
&nbsp;
</div>

<div ID="ch_num" style="position:absolute;visibility:hidden;display:none">
	<form name="ch_numForm">
		<table class="table table-bordered" bgcolor="#e0ffff" cellspacing=1>
		<tr>
			<td valign=top nowrap>
				<a href="JavaScript:void(0)" onClick="hideElement('ch_num');" style="text-decoration:none"><B>×</B></a><br>
				<select name="AMOUNT" size=13 onchange="chNumDo()"></select>
			</td>
		</tr>
		</table>
	</form>
</div>

<table class="table table-bordered">
<tr valign="top">
<td class="InputCell">

<form action="{$this_file}" method="post" name="InputPlan">
	<input type="hidden" name="mode" value="command">
	<input type="hidden" name="COMARY" value="comary">
	<input type="hidden" name="DEVELOPEMODE" value="javascript">

	<div class="text-center">

	<button type="submit" name="sendProj" class="btn btn-primary btn-block">計画送信</button>

	<hr>

	<h3>コマンド入力</h3>
	<ul class="list-inline">
		<li><b><a href="javascript:void(0);" onclick="cominput(InputPlan,1)">挿入</a></b>
		<li><b><a href="javascript:void(0);" onclick="cominput(InputPlan,2)">上書き</a></b>
		<li><b><a href="javascript:void(0);" onclick="cominput(InputPlan,3)">削除</a></b>
	</ul>

	<hr>

	<h4>計画番号</h4>
	<select name="number">
END;
        // 計画番号
        for ($i = 0; $i < $init->commandMax;) {
            echo '<option value="', $i, '">', ++$i, '</option>', "\n";
        }

        $open = "";
        if (isset($data['MENUOPEN'])) {
            $open = ($data['MENUOPEN'] == 'on')? 'checked': '';
        }

        echo <<<END
	</select>

	<hr>

	<h3>開発計画</h3>
	<div>
		<label class="checkbox-inline"><input type="checkbox" name="NAVIOFF" $open> NaviOff</label>
		<label class="checkbox-inline"><input type="checkbox" name="MENUOPEN" $open> PopupOff</label>
	</div>

	<select name="menu" onchange="SelectList(InputPlan)">
	<option value="">全種類</option>
END;
        for ($i = 0; $i < $com_count; $i++) {
            [$aa, $tmp] = explode(",", $init->commandDivido[$i], 2);
            echo "<option value=\"$i\">{$aa}</option>\n";
        }
        echo <<<END
	</select>
	<br>
	<select name="commands">
		<option></option>
	</select>

	<hr>

	<b>座標： (</b>
	<select name="POINTX">
END;
        for ($i = 0; $i < $init->islandSize; $i++) {
            if (isset($data['defaultX'])) {
                if ($i == $data['defaultX']) {
                    echo "<option value=\"$i\" selected>$i</option>\n";
                } else {
                    echo "<option value=\"$i\">$i</option>\n";
                }
            } else {
                echo "<option value=\"$i\">$i</option>\n";
            }
        }
        echo "</select>, <select name=\"POINTY\">\n";
        for ($i = 0; $i < $init->islandSize; $i++) {
            if (isset($data['defaultY'])) {
                if ($i == $data['defaultY']) {
                    echo "<option value=\"$i\" selected>$i</option>\n";
                } else {
                    echo "<option value=\"$i\">$i</option>\n";
                }
            } else {
                echo "<option value=\"$i\">$i</option>\n";
            }
        }

        echo <<<END
	</select><b> )</b>

	<hr>

	<b>数量</b>
	<select name="AMOUNT">
END;
        // 数量
        for ($i = 0; $i < 100; $i++) {
            echo "<option value=\"$i\">$i</option>\n";
        }

        // 船舶数
        $ownship = 0;
        for ($i = 0; $i < $init->shipKind; $i++) {
            $ownship += $island['ship'][$i];
        }
        echo <<<END
	</select>

	<hr>

	<h3>目標の島</h3>
	<select name="TARGETID" onchange="settarget(this);">$hako->targetList</select><br>
	<input type="button" class="btn btn-default btn-sm" value="目標捕捉" onClick="javascript: targetopen();">

	<hr>

	<h3>コマンド移動</h3>
	<ul class="list-inline">
		<li><a href="javascript:void(0);" onclick="cominput(InputPlan,4)" style="text-decoration:none"> ▲ </a></li>
		<li><a href="javascript:void(0);" onclick="cominput(InputPlan,5)" style="text-decoration:none"> ▼ </a></li>
	</ul>

	<hr>

	<input type="hidden" name="ISLANDID" value="{$island['id']}">
	<input type="hidden" name="PASSWORD" value="{$data['defaultPassword']}">
	<button type="submit" name="sendProj" class="btn btn-primary btn-block">計画送信</button>

	<p>最後に<span style="color:#c7243a;">計画送信ボタン</span>を<br>押すのを忘れないように。</p>

</div>
</form>

<ul class="list-unstyled">
<li>ミサイル発射上限数[<strong> {$island['fire']} </strong>]発</li>
<li>所有船舶数[<strong> {$ownship} </strong>]隻</li>
</ul>

<!-- <p>
<a href="javascript:void(0)" title='数字=数量　BS=一つ前削除
DEL=削除　INS=資金繰り
A=整地　J=地ならし
K=掘削　U=埋め立て
B=伐採　P=植林
N=農場整備　I=工場建設
S=採掘場整備
D=防衛施設建設
M=ミサイル基地建設
F=海底基地建設'>ショートカットキー入力簡易説明</a>
</p> -->

</td>
<td class="MapCell" id="plan" onmouseout="mc_out();return false;">
END;
        $this->islandMap($hako, $island, 1); // 島の地図、所有者モード
        $comment = $hako->islands[$number]['comment'];
        echo <<<END
</td>
<td id="plan" class="CommandCell">
<div id="IsSynced"></div>
</td>
</tr>
</table>

<hr>

<div id='CommentBox'>
<h2>コメント更新</h2>
<form action="{$this_file}" method="post">
<div class="input-group">
<input type="text" name="MESSAGE" class="form-control" size="80" value="{$island['comment']}" placeholder="コメントする">
<input type="hidden" name="PASSWORD" value="{$data['defaultPassword']}">
<input type="hidden" name="mode" value="comment">
<input type="hidden" name="ISLANDID" value="{$island['id']}">
<input type="hidden" name="DEVELOPEMODE" value="javascript">
<span class="input-group-btn"><input type="submit" class="btn btn-primary" value="コメント更新"></span>
</div>
</form>
</div>
END;
    }
}


class HtmlAdmin extends HTML
{
    public function render(): void
    {
        global $init;

        $menuList  = [
            'データ管理'       => '/hako-mente.php',
            'アクセスログ閲覧' => '/hako-axes.php',
            'プレゼント'       => '/hako-present.php',
            'マップエディタ'   => '/hako-edit.php',
            'BF管理'           => '/hako-bf.php',
            '島預かり管理'     => '/hako-keep.php'
        ];
        require_once VIEWS.'/admin/top.php';
    }
}

class HtmlPresent extends HTML
{
    public function main($data, $hako): void
    {
        global $init;
        $this_file = $init->baseDir . "/hako-present.php";
        $main_file = $init->baseDir . "/hako-main.php";

        $width  = $init->islandSize * 32 + 50;
        $height = $init->islandSize * 32 + 100;
        $defaultTarget = '';

        require_once VIEWS.'/admin/present/main.php';
    }
}



class HtmlMente extends HTML
{
    public function enter(): void
    {
        global $init;
        $this_file = $init->baseDir.'/hako-mente.php';

        parent::pageTitle($init->title, 'データ管理ツール');
        require_once VIEWS.'/admin/Maintenance/enter.php';
    }

    public function main($data): void
    {
        global $init;
        $this_file = $init->baseDir."/hako-mente.php";
        $dirName = $init->dirName;
        parent::pageTitle($init->title, 'メンテナンスツール');

        // データ保存用ディレクトリの存在チェック
        if (!is_dir($dirName)) {
            if (!@mkdir($dirName, 0775, true)) {
                \Util::makeTagMessage("データ保存用ディレクトリが存在せず、また何らかの理由で作成に失敗しました。\nゲーム設定を再度確認した上で、サーバー管理者にお問合せください。", 'danger');
                \HTML::footer();
                exit;
            }
        }
        // データ保存用ディレクトリのパーミッションチェック
        if (!is_writable($dirName) || !is_readable($dirName)) {
            \Util::makeTagMessage("データ保存用ディレクトリに対する適切な操作権限を所持していません。\nサーバー管理者にお問合せください。", 'danger');
            \HTML::footer();
            exit;
        }

        if (is_file($dirName.'/hakojima.dat')) {
            $this->dataPrint($data);
        } else {
            echo <<<EOT
<hr>
<form action="$this_file" method="post">
    <input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
    <input type="hidden" name="mode" value="NEW">
    <button type="submit" class="btn btn-info">新しくデータを作る</button>
</form>
EOT;
        }
        // バックアップデータがあれば表示
        $dir = opendir(dirname($dirName));
        $dirCld = false !== mb_strpos($dirName, "/") ? mb_substr(mb_strrchr($dirName, "/"), 1) : $dirName;
        while (false !== ($dn = readdir($dir))) {
            $_dirName = preg_quote($dirCld);
            if (preg_match("/{$_dirName}\.bak(.*)$/", $dn, $matches)) {
                if (is_file("$dirName.bak{$matches[1]}/hakojima.dat")) {
                    $this->dataPrint($data, $matches[1]);
                }
            }
        }
        closedir($dir);
    }

    // 表示モード
    public function dataPrint($data, $suf = ""): void
    {
        global $init;
        $this_file = $init->baseDir."/hako-mente.php";

        println('<hr>', PHP_EOL, '<section>');
        if (strcmp($suf, "") == 0) {
            $fp = fopen($init->dirName.'/hakojima.dat', "r");
            println('<h2>現役データ</h2>');
        } else {
            $fp = fopen($init->dirName.".bak{$suf}/hakojima.dat", "r");
            println('<h2>バックアップ <small>（bak', $suf, '）</small></h2>');
        }
        $lastTurn = (int)rtrim(fgets($fp, READ_LINE));
        $lastTime = (int)rtrim(fgets($fp, READ_LINE));
        fclose($fp);
        $timeString = self::timeToString($lastTime);

        echo <<<END
<h3>ターン：$lastTurn</h3>
<p><strong>最終更新時刻</strong>：$timeString</p>
<form action="$this_file" method="post" class="form-group">
    <input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
    <input type="hidden" name="mode" value="DELETE">
    <input type="hidden" name="NUMBER" value="$suf">
    <button type="submit" class="btn btn-danger btn-sm">このデータを削除</button>
</form>
END;
        if (strcmp($suf, "") == 0) {
            $date = date('Y-m-d', $lastTime);
            $time = date('H:i', $lastTime);
            echo <<<END
<h4>最終更新時刻の変更</h4>
<form action="$this_file" method="post" class="form-inline">
	<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
	<input type="hidden" name="mode" value="NTIME">
    <input type="date" name="date" value="$date" class="form-control" pattern="\d{4}-\d{2}-\d{2}">
    <input type="time" name="time" value="$time" class="form-control" pattern="\d{2}:\d{2}">
    <button type="submit" class="btn btn-warning btn-sm">変更</button>
</form>
END;
        } else {
            echo <<<END
<form action="$this_file" method="post" class="form-group">
    <input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
    <input type="hidden" name="NUMBER" value="$suf">
    <input type="hidden" name="mode" value="CURRENT">
    <button type="submit" class="btn btn-warning">このデータを現役に</button>
</form>
END;
        }
        println('</section>');
    }
}

class HtmlAxes extends HTML
{
    public function passwdChk(): void
    {
        global $init;
        $this_file = $init->baseDir.'/hako-axes.php';
        parent::pageTitle($init->title, 'アクセスログ');

        echo <<<END
<form action="$this_file" method="post" class="form-inline">
    <label>パスワード：
    <input type="password" size="32" name="PASSWORD" class="form-control"></label>
    <input type="hidden" name="mode" value="auth">
    <button type="submit" class="btn btn-default">サインイン</button>
</form>
END;
    }

    public function main($data): void
    {
        global $init;
        parent::pageTitle($init->title, 'アクセスログ');

        require_once VIEWS.'/admin/Axes.php';
    }
}



class HtmlBF extends HTML
{
    public function main($data, $hako): void
    {
        global $init;
        $this_file = $init->baseDir.'/hako-bf.php';
        require_once VIEWS.'/admin/bf.php';
    }
}



class HTMLKeep extends HTML
{
    public function main($data, $hako): void
    {
        global $init;
        $this_file = $init->baseDir.'/hako-keep.php';
        require_once VIEWS.'/admin/keep.php';
    }
}



class HtmlAlly extends HTML
{
    private $this_file;

    public function __construct()
    {
        global $init;
        $this->this_file = $init->baseDir . '/hako-ally.php';
    }

    /**
     * 初期画面
     */
    public function allyTop($hako, $data): void
    {
        global $init;

        require VIEWS.'Alliance/Index.php';
    }

    /**
     * 同盟の状況
     */
    public function allyInfo($hako, $view_ally_num = 0)
    {
        global $init;

        $alliances_number = (int)$hako->allyNumber;
        $alliances = [];
        for ($i = 0; $i < $alliances_number; $i++) {
            if ($view_ally_num && ($i != $hako->idToAllyNumber[$view_ally_num])) {
                continue;
            }
            $alliance = $hako->ally[$i];

            $alliance['members'] = (int)$alliance['number'];
            $alliance['owner']   = $alliance['oName'];
            $alliance['population'] = 0;

            for ($ii = 0; $ii < $alliance['members']; $ii++) {
                $member_id = $alliance['memberId'][$ii];
                $member_island = $hako->islands[$hako->idToNumber[$member_id]];
                $alliance['population'] += $member_island['pop'];
            }
            unset($ii);

            $alliances[] = $alliance;
        }
        unset($i);

        return [$alliances_number, $alliances];
    }

    /**
     * 同盟の情報
     */
    public function detail($hako, $data): void
    {
        global $init;

        $num = $data['ALLYID'];
        $alliance = $hako->ally[$hako->idToAllyNumber[$num]];
        $islands = [];
        $unit = '0'.$init->unitPop;

        $alliance['title'] = $alliance['title'] !== '' ? $alliance['title'] : '盟主からのメッセージ';

        for ($i = 0; $i < $hako->allyNumber; $i++) {
            if ($hako->ally[$i]['id'] === $alliance['id']) {
                $alliance['rank'] = $i + 1;

                break;
            }
        }

        foreach ($alliance['memberId'] as $id) {
            $number = $hako->idToNumber[$id];
            if ($number < 0) {
                continue;
            }

            $island = [];
            $isl = $hako->islands[$number];
            $island['pop']   = $isl['pop'];
            $island['area']  = $isl['area'];
            $island['money'] = Util::aboutMoney($isl['money']);
            $island['food']  = $isl['food'];
            $island['farm']  = (int)$isl['farm'] === 0 ? $init->notHave : ($isl['farm'].$unit);
            $island['factory']  = (int)$isl['factory'] === 0 ? $init->notHave : ($isl['factory'].$unit);
            $island['commerce'] = (int)$isl['commerce'] === 0 ? $init->notHave : ($isl['commerce'].$unit);
            $island['mountain'] = (int)$isl['mountain'] === 0 ? $init->notHave : ($mountain.$unit);
            $island['hatuden']  = (int)$isl['hatuden'] * 1000 . 'kW';
            $island['rank']   = $number + 1;
            $island['name']   = Util::islandName($isl, $hako->ally, $hako->idToAllyNumber);
            $island['absent'] = $isl['absent'];
            $island['id']     = $isl['id'];

            if ((int)$island['absent'] === 0) {
                $island['name'] = '<span class="islName"><a href="'.$init->baseDir.'/hako-main.php?Sight='.$island['id'].'">'.$island['name'].'</a></span>';
            } else {
                $island['name'] = '<span class="islName2"><a href="'.$init->baseDir.'/hako-main.php?Sight='.$island['id'].'">'.$island['name'].'</a>（'.$island['absent'].'）</span>';
            }

            $islands[] = $island;
        }
        unset($island);
        require VIEWS . 'Alliance/Detail.php';
    }

    //--------------------------------------------------
    // 同盟コメントの変更
    //--------------------------------------------------
    public function tempAllyPactPage($hako, $data): void
    {
        global $init;
        $this_file  = $init->baseDir . "/hako-ally.php";

        $num = $data['ALLYID'];
        $ally = $hako->ally[$hako->idToAllyNumber[$num]];
        $allyMessage = $ally['message'];

        $allyMessage = str_replace("<br>", "\n", $allyMessage);
        $allyMessage = str_replace("&amp;", "&", $allyMessage);
        $allyMessage = str_replace("&lt;", "<", $allyMessage);
        $allyMessage = str_replace("&gt;", ">", $allyMessage);
        $allyMessage = str_replace("&quot;", "\"", $allyMessage);
        $allyMessage = str_replace("&#039;", "'", $allyMessage);

        $data['defaultPassword'] = $data['defaultPassword'] ?? "";
        echo <<<END
<p class='text-center big'>コメント変更（<span class="islName">{$ally['name']}</span>）</p>

<div id='changeInfo'>
<table>
<tr>
	<td class="M">
		<FORM action="{$this_file}" method="POST">
			<B>盟主パスワードは？</B><BR>
			<INPUT TYPE="password" NAME="Allypact" VALUE="{$data['defaultPassword']}" SIZE=32 MAXLENGTH=32 class="f form-control">
			<INPUT TYPE="hidden"  NAME="ALLYID" VALUE="{$ally['id']}">
			<INPUT TYPE="submit" VALUE="送信" NAME="AllypactButton"><BR>

			<B>コメント</B><small>（全角{$init->lengthAllyComment}字まで：トップページの「各同盟の状況」欄に表示されます）</small>
			<INPUT TYPE="text" NAME="ALLYCOMMENT" VALUE="{$ally['comment']}" MAXLENGTH="50" class="form-control">

			<B>メッセージ・盟約など</B><small>（「同盟の情報」欄の上に表示されます）</small><BR>
			タイトル<small>（全角{$init->lengthAllyTitle}字まで）</small>
			<INPUT TYPE="text" NAME="ALLYTITLE"  VALUE="{$ally['title']}" MAXLENGTH="50" class="form-control">

			メッセージ<small>（全角{$init->lengthAllyMessage}字まで）</small>
			<TEXTAREA COLS=50 ROWS=16 NAME="ALLYMESSAGE" WRAP="soft" class="form-control">{$allyMessage}</TEXTAREA>
			<BR>
			「タイトル」を空欄にすると『盟主からのメッセージ』というタイトルになります。<BR>
			「メッセージ」を空欄にすると「同盟の情報」欄には何も表示されなくなります。
		</FORM>
	</td>
	</tr>
</table>
</DIV>
END;
    }

    //--------------------------------------------------
    // 同盟の結成・変更・解散・加盟・脱退
    //--------------------------------------------------
    public function register($hako, $data)
    {
        global $init;

        function hsc($str)
        {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }

        $denying_name_words = '["' . implode('","', $init->denying_name_words) . '"]';
        $regex_denying_name_words = $init->regex_denying_name_words;

        require VIEWS . 'Alliance/Register.php';
    }

    public function newAllyTop($hako, $data): void
    {
        global $init;
        $this_file  = $init->baseDir . "/hako-ally.php";

        $adminMode = false;

        $jsAllyList      = "";
        $jsAllyIdList    = "";
        $jsAllyMarkList  = "";
        $jsAllyColorList = "";

        $data['defaultPassword'] = $data['defaultPassword'] ?? "";
        if (AllyUtil::checkPassword("", $data['defaultPassword'])) {
            // 管理者の判定は、お菓子のパスワード、盟主の変更可
            $adminMode = true;
        } elseif (!$init->allyUse) {
            $this->allyTop($hako, $data);
        }

        $jsIslandList    = "";
        $num = (int)$hako->islandNumber;
        for ($i=0; $i<$num; $i++) {
            $name = $hako->islands[$i]['name'];
            $name = preg_replace("/'/", "\'", $name);
            $id = $hako->islands[$i]['id'];
            $jsIslandList .= "island[$id] = '$name';\n";
        }
        $data['defaultID'] = $data['defaultID'] ?? '';
        $n = $hako->idToAllyNumber[$data['defaultID']] ?? '';

        if ($n == '') {
            $allyname = '';
            $defaultMark = $hako->ally[0];
            $defaultAllyId = '';
        } else {
            $allyname = $hako->ally[$n]['name'];
            $allyname = preg_replace("/'/", "\'", $allyname);
            $defaultMark = $hako->ally[$n]['mark'];
            $defaultAllyId = $hako->ally[$n]['id'];
        }
        $defaultMark = '';
        $markList = "";
        foreach ($init->allyMark as $aMark) {
            $s = $aMark == $defaultMark ? ' selected' : '';
            $markList .= "<option value=\"$aMark\"$s>$aMark</option>\n";
        }

        $max = 201;
        if ($hako->allyNumber) {
            $jsAllyList      = "var ally = [";
            $jsAllyIdList    = "var allyID = [";
            $jsAllyMarkList  = "var allyMark = [";
            $jsAllyColorList = "var allyColor = [";
            for ($i=0; $i<count($hako->ally); $i++) {
                $s = "";
                if ($hako->ally[$i]['id'] == $defaultAllyId) {
                    $s = ' selected';
                }
                $allyList = "";
                $allyList .= "<option value=\"$i\"$s>{$hako->ally[$i]['name']}</option>\n";
                $jsAllyList .= "'{$hako->ally[$i]['name']}'";
                $jsAllyIdList .= "{$hako->ally[$i]['id']}";
                $jsAllyMarkList .= "'{$hako->ally[$i]['mark']}'";
                $jsAllyColorList .= "[";
                for ($j=0; $j<6; $j++) {
                    $jsAllyColorList .= '\'' . mb_substr($hako->ally[$i]['color'], $j, 1) . '\'';
                    if ($j < 5) {
                        $jsAllyColorList .= ',';
                    }
                }
                $jsAllyColorList .= "]";
                if ($i < count($hako->ally)) {
                    $jsAllyList .= ",\n";
                    $jsAllyIdList .= ",\n";
                    $jsAllyMarkList .= ",\n";
                    $jsAllyColorList .= ",\n";
                }
                if ($max <= $hako->ally[$i]['id']) {
                    $max = $hako->ally[$i]['id'] + 1;
                }
            }
            $jsAllyList .= "];\n";
            $jsAllyIdList .= "];\n";
            $jsAllyMarkList .= "];\n";
            $jsAllyColorList .= "];\n";
        }
        $str1 = $adminMode ? '（メンテナンス）' : $init->allyJoinComUse ? '' : '・加盟・脱退';

        $makeCost = $init->costMakeAlly ? "{$init->costMakeAlly}{$init->unitMoney}" : '無料';

        $keepCost = $init->costKeepAlly ? "{$init->costKeepAlly}{$init->unitMoney}" : '無料';

        $joinCost = isset($init->comCost[$init->comAlly]) ? "{$init->comCost[$init->comAlly]}{$init->unitMoney}" : '無料';

        $joinStr = $init->allyJoinComUse ? '' : '加盟・脱退の際の費用は、<span class="cash">' . $joinCost . '</span>です。<br>';

        $str3 = $adminMode ? <<<END
特殊パスワードは？（必須）<br>
<input type="password" name="OLDPASS" value="{$data['defaultPassword']}" size=32 class=f><br>
同盟
END
: <<<END
<div class="alert alert-info">
  <span class="attention">（注意）</span><br>
  同盟の結成・変更の費用は、<span class="cash">$makeCost</span>です。<br>
  また、毎ターン必要とされる維持費は<span class="cash">$keepCost</span>です。<br>
  （維持費は同盟に所属する島で均等に負担することになります）<br>
  $joinStr
</div>


あなたの島は？（必須）<BR>
<select name="ISLANDID" onChange="colorPack()" onClick="colorPack()">
{$hako->islandList}
</select><BR>あなた
END;
        $str0 = ($adminMode || ($init->allyUse == 1)) ? '結成・' : '';
        echo <<<END
<p class="text-center big">同盟の{$str0}変更・解散$str1</p>

<DIV ID='changeInfo'>
<table border=0 width=50%><tr><td class="M"><P>
<FORM name="AcForm" action="{$this_file}" method="POST">
{$str3}のパスワードは？（必須）<BR>
<INPUT TYPE="password" NAME="PASSWORD" SIZE="32" MAXLENGTH="32" class="f" class="form-control">
END;
        if ($hako->allyNumber) {
            $str4 = $adminMode ? '・結成・変更' : $init->allyJoinComUse ? '' : '・加盟・脱退';
            $str5 = ($adminMode || $init->allyJoinComUse) ? '' : '<INPUT TYPE="submit" VALUE="加盟・脱退" NAME="JoinAllyButton" class="btn btn-default">';
            echo <<<END
<BR>
<BR><B>［解散{$str4}］</B>
<BR>どの同盟ですか？<BR>
<SELECT NAME="ALLYNUMBER" onChange="allyPack()" onClick="allyPack()">
{$allyList}
</SELECT>
<BR>
<INPUT TYPE="submit" VALUE="解散" NAME="DeleteAllyButton" class="btn btn-danger">
{$str5}
<BR>
END;
        }
        $str7 = $adminMode ? "盟主島の変更（上のメニューで同盟を選択）<BR> or 同盟の新規作成（上のメニューは無効）<BR><SELECT NAME=\"ALLYID\"><option value=\"$max\">新規作成\n{$hako->islandList}</option></SELECT><BR>" : "<BR><B>［{$str0}変更］</B><BR>";
        echo <<<END
<BR>
{$str7}
同盟の名前（変更）<small>(全角{$init->lengthAllyName}字まで）</small><BR>
<INPUT TYPE="text" NAME="ALLYNAME" VALUE="$allyname" SIZE={(int)$init->lengthAllyName + 1} MAXLENGTH={$init->lengthAllyName} class="form-control"><BR>
マーク（変更）<BR>
<SELECT NAME="MARK" onChange="colorPack()" onClick="colorPack()">{$markList}</SELECT>
<br>
マークの色コード（変更）<BR>

<p>表示サンプル：『<span class="number"><span id="SampleSign" style="font-weight:bold;"></span> <span name="sampleName">●●●</span>{$init->nameSuffix}</span>』</p>
<input type="color" name="colorCode" onChange="colorPack()" onClick="colorPack()" class="form-control" value="$">

<input type="submit" value="結成 （変更）" name="NewAllyButton" class="btn btn-primary">
<script>
function colorPack() {
	let island = new Array(128);
	{$jsIslandList}

    let color = document.forms.AcForm.colorCode.value;
	let mark = document.forms.AcForm.MARK.value;
	let number = document.forms.AcForm.ISLANDID.value;
    const sampleSign = document.querySelector('form[name="AcForm"] #SampleSign');

    sampleSign.textContent = mark;
    sampleSign.style.color = color;

	// var str = '表示サンプル：『<span class="number"><span style="font-weight:bold;color:'+ str +'">' + mark + '</span> ' + island[number] + '{$init->nameSuffix}</span>』';

	return true;
}
function allyPack() {
	{$jsAllyList}
	{$jsAllyMarkList}
	{$jsAllyColorList}
	document.forms.AcForm.ALLYNAME.value = ally[document.forms.AcForm.ALLYNUMBER.value];
	document.forms.AcForm.MARK.value = allyMark[document.forms.AcForm.ALLYNUMBER.value];
	document.forms.AcForm.colorCode.value = allyColor[document.forms.AcForm.ALLYNUMBER.value];

	colorPack();
	return true;
}
</form>

</td>
</tr>
</table>
</div>
END;
    }
}
