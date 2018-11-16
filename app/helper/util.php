<?php

declare(strict_types=1);
/**
 * 箱庭諸島 S.E - 各種ユーティリティ定義用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */
final class Util
{
    /**
     * 資金を丸めて表示する
     * @param  integer $money 資金額
     * @return string         丸めた文字列
     */
    public static function aboutMoney(int $money = 0): string
    {
        global $init;
        $digit = (int)$init->moneyMode;

        return ($digit <= 0)? $money.$init->unitMoney
                : (($money < $digit)? "推定{$digit}{$init->unitMoney}未満"
                : '推定'.round($money / $digit) * $digit . $init->unitMoney);
    }

    /**
     * 経験値からミサイル基地レベルを算出
     * @param  [type]  $kind ミサイル基地種別
     * @param  integer $exp  経験値
     * @return integer       対応した基地レベル値
     */
    public static function expToLevel(int $kind, int $exp): int
    {
        global $init;

        // ミサイル基地
        if ($kind == $init->landBase) {
            for ($i = $init->maxBaseLevel; $i > 1; $i--) {
                if ($exp >= $init->baseLevelUp[$i - 2]) {
                    return $i;
                }
            }

            // 海底基地
        } else {
            for ($i = $init->maxSBaseLevel; $i > 1; $i--) {
                if ($exp >= $init->sBaseLevelUp[$i - 2]) {
                    return $i;
                }
            }
        }

        return 1;
    }

    /**
     * 怪獣の種類・名前・体力を算出
     * @param  [type] $lv 地形メタデータ
     * @return [type]     [description]
     */
    public static function monsterSpec($lv)
    {
        global $init;

        // 種類
        $kind = intdiv($lv, 100);
        // 名前
        $name = $init->monsterName[$kind];
        // 体力
        $hp = $lv % 100;

        // monster rank
        $rank = $kind <= $init->disMonsBorder1 ? 1
            : ($kind <= $init->disMonsBorder2 ? 2
                : ($kind <= $init->disMonsBorder3 ? 3
                    :($kind <= $init->disMonsBorder4 ? 4
                        : 5)));

        return ['kind' => $kind, 'name' => $name, 'hp' => $hp, 'rank' => $rank];
    }

    /**
     * 島の名前からIDを逆引き
     * @param          $hako ゲーム総合データ
     * @param  string  $name 島の名前
     * @return integer       該当の島ID（>=0）、なければ-1
     */
    public static function nameToNumber($hako, $name)
    {
        for ($i = 0; $i < $hako->islandNumber; $i++) {
            if (strcmp($name, $hako->islands[$i]['name']) == 0) {
                return $i;
            }
        }

        return -1;
    }

    /**
     * 島名を返す
     * @param  [type] $island         [description]
     * @param  [type] $ally           [description]
     * @param  [type] $idToAllyNumber [description]
     * @return [type]                 [description]
     */
    public static function islandName($island, $ally, $idToAllyNumber)
    {
        $name = '';
        foreach ($island['allyId'] as $id) {
            $i = $idToAllyNumber[$id];
            $mark  = $ally[$i]['mark'];
            $color = $ally[$i]['color'];
            $name .= '<span style="color:'.$color.'";>'.$mark.'</span> ';
        }
        $name .= $island['name'].'島';

        return $name;
    }

    /**
     * パスワードチェック
     * @param  string $p1
     * @param  string $p2
     * @return bool
     */
    public static function checkPassword(string $p1, string $p2): bool
    {
        global $init;

        if (empty($p2)) {
            return false;
        }

        if (!file_exists($init->passwordFile)) {
            \HakoError::probrem();

            return false;
        }
        $fp = fopen($init->passwordFile, "r");
        $masterPasswd = rtrim(fgets($fp, READ_LINE));
        fclose($fp);

        // マスターパスワードチェック
        if (password_verify($p2, $masterPasswd)) {
            return true;
        }

        // 通常のパスワードチェック
        $isLegacyHash = '$2y$10$' !== mb_substr($p1, 0, 7);
        if (!$isLegacyHash) {
            return password_verify($p2, $p1);
        }
        if (strcmp($p1, Util::encode($p2)) == 0) {
            return true;
        }

        return false;
    }

    public static function checkAdminPassword(string $p): bool
    {
        global $init;

        if (!file_exists($init->passwordFile)) {
            \HakoError::probrem();

            return false;
        }
        $fp = fopen($init->passwordFile, "r");
        $AdminPassword = rtrim(fgets($fp, READ_LINE));
        fclose($fp);

        return password_verify($p, $AdminPassword);
    }

    /**
     * 特殊パスワードチェック
     * @param  string $p 入力パスワード
     * @return bool
     */
    public static function checkSpecialPassword(string $p = ""): bool
    {
        global $init;

        if (empty($p)) {
            return false;
        }
        if (!file_exists($init->passwordFile)) {
            \HakoError::probrem();

            return false;
        }
        $fp = fopen($init->passwordFile, "r");
        $specialPasswd = rtrim(fgets($fp, READ_LINE));//1行目を破棄
        $specialPasswd = rtrim(fgets($fp, READ_LINE));
        fclose($fp);

        return password_verify($p, $specialPasswd);
    }

    /**
     * パスワードのエンコード
     */
    public static function encode(string $s, bool $isLegacy = true): string
    {
        return ($isLegacy)? crypt($s, 'h2') : password_hash($s, PASSWORD_DEFAULT, ['cost'=>10]);
    }

    /**
     * [0, num-1]の乱数生成
     * @param  int $num 正の整数（通例2以上）
     * @return int      [0, $num-1]の範囲の整数
     */
    public static function random(int $num = 0, int $max = PHP_INT_MIN): int
    {
        if ($max !== PHP_INT_MIN) {
            return random_int($num, $max);
        } else {
            return $num > 1 ? random_int(0, $num - 1) : 0;
        }
    }

    public static function hasIslandAttribute($island, array $flags = [], array $opt = []): bool
    {
        global $init;

        $match = false;
        foreach ($flags as $f) {
            switch ($f) {
                case "newbie":
                    if (!array_key_exists("islandTurn", $opt)) {
                        throw new \InvalidArgumentException("You must set \$opt['islandTurn'] when you'll check `newbie`.");
                    }
                    if (($opt["islandTurn"] - $island['starturn']) < $init->noAssist) {
                        $match = true;
                    }

                    break;
                case "sleep": // "keep"
                    if ($island["keep"]) {
                        $match = true;
                    }

                    break;
                case "monster":
                    if (!array_key_exists("level", $opt)) {
                        throw new \InvalidArgumentException("You must set \$opt['level'] when you'll check `monster`.");
                    }
                    $level = "disMonsBorder".$opt["level"];
                    if (!property_exists($init, $level)) {
                        throw new \InvalidArgumentException("Don't exist `\$init->{$level}`, probably wrong \$opt['level'] (=> {$opt['level']})?");
                    }
                    if ($island["pop"] >= $init->$level) {
                        $match = true;
                    }
                    unset($level);

                    break;
                default:
                    throw new \InvalidArgumentException("Wrong attribute: `{$f}`.");
            }
        }
        unset($flags, $opt, $f);

        return $match;
    }


    /**
     * ランダムな座標配列を生成
     * @return [type] [description]
     */
    public static function makeRandomPointArray()
    {
        global $init;

        $rx = $ry = [];
        for ($i = 0; $i < $init->islandSize; $i++) {
            for ($j = 0; $j < $init->islandSize; $j++) {
                $rx[$i * $init->islandSize + $j] = $j;
            }
        }
        for ($i = 0; $i < $init->islandSize; $i++) {
            for ($j = 0; $j < $init->islandSize; $j++) {
                $ry[$j * $init->islandSize + $i] = $j;
            }
        }

        for ($i = $init->pointNumber; --$i;) {
            $j = Util::random($i + 1);
            if ($i != $j) {
                $tmp = $rx[$i];
                $rx[$i] = $rx[$j];
                $rx[$j] = $tmp;
                $tmp = $ry[$i];
                $ry[$i] = $ry[$j];
                $ry[$j] = $tmp;
            }
        }

        return [$rx, $ry];
    }

    //---------------------------------------------------
    // ランダムな島の順序を生成
    //---------------------------------------------------
    public static function randomArray($n = 1)
    {
        // 初期値
        for ($i = 0; $i < $n; $i++) {
            $list[$i] = $i;
        }
        // シャッフル
        for ($i = 0; $i < $n; $i++) {
            $j = Util::random($n - 1);
            if ($i != $j) {
                $tmp = $list[$i];
                $list[$i] = $list[$j];
                $list[$j] = $tmp;
            }
        }

        return $list;
    }

    //---------------------------------------------------
    // コマンドを前にずらす
    //---------------------------------------------------
    public static function slideFront(&$command, $number = 0): void
    {
        global $init;

        // それぞれずらす
        array_splice($command, $number, 1);

        // 最後に資金繰り
        $command[$init->commandMax - 1] = [
            'kind'   => $init->comDoNothing,
            'target' => 0,
            'x'      => 0,
            'y'      => 0,
            'arg'    => 0
        ];
    }

    //---------------------------------------------------
    // コマンドを後にずらす
    //---------------------------------------------------
    public static function slideBack(&$command, $number = 0): void
    {
        global $init;

        // それぞれずらす
        if ($number == count($command) - 1) {
            return;
        }
        for ($i = $init->commandMax - 1; $i > $number; $i--) {
            $command[$i] = $command[$i - 1];
        }
        $command[$i] = [
            'kind'   => $init->comDoNothing,
            'target' => 0,
            'x'      => 0,
            'y'      => 0,
            'arg'    => 0
        ];
    }

    //---------------------------------------------------
    // 船情報のUnpack
    //---------------------------------------------------
    public static function navyUnpack($lv)
    {
        global $init;

        // bit 意味
        //-----------
        //  5  島ID
        //  3  種類
        //  4  耐久力
        //  4  経験値
        //  4  フラグ
        // 20  合計

        $flag = $lv & 0x0f;
        $lv >>= 4;
        $exp  = $lv & 0x0f;
        $lv >>= 4;
        $hp   = $lv & 0x0f;
        $lv >>= 4;
        $kind = $lv & 0x07;
        $lv >>= 3;
        $id   = $lv & 0x1f;

        return [$id, $kind, $hp, $exp, $flag];
    }

    //---------------------------------------------------
    // 船情報のPack
    //---------------------------------------------------
    public static function navyPack($id, $kind, $hp, $exp, $flag)
    {
        global $init;

        // bit 意味
        //-----------
        //  5  島ID
        //  3  種類
        //  4  耐久力
        //  4  経験値
        //  4  フラグ
        // 20  合計

        if ($id>0x1f) {
            throw new Exception("船籍ID不正", 1);
        }

        $exp  = min($exp, 15);
        $flag = min($flag, 15);

        $lv   = 0;
        $lv |= $id   & 0x1f;
        $lv <<= 3;
        $lv |= $kind & 0x07;
        $lv <<= 4;
        $lv |= $hp   & 0x0f;
        $lv <<= 4;
        $lv |= $exp  & 0x0f;
        $lv <<= 4;
        $lv |= $flag & 0x0f;

        return $lv;
    }

    /**
     * 島の船データから、災害船舶（海賊船とか）を所持しているかを判定する
     * @param  arr     $ships 島データ内、船舶部分
     * @return boolean        災害船舶が1隻でも存在していたらtrue
     */
    public static function hasBadShip($ships)
    {
        global $init;
        $badShipsId = $init->shipKind;
        $badShips   = 0;
        for ($i=$badShipsId, $len = count($ships); $i < $len; $i++) {
            if (is_numeric($ships[$i]) && $ships[$i] > 0) {
                $badShips++;
            }
        }

        return $badShips !== 0;
    }

    /**
     * ファイルをロックする
     */
    public static function lock()
    {
        global $init;

        $fp = fopen($init->dirName."/lock.dat", "w");

        for ($count = 0; $count < LOCK_RETRY_COUNT; $count++) {
            if (flock($fp, LOCK_EX)) {
                // ロック成功
                return $fp;
            }
            // 一定時間sleepし、ロックが解除されるのを待つ
            // 乱数時間sleepすることで、ロックが何度も衝突しないようにする
            usleep(random_int(0, 100) * 1000);
        }
        // ロック失敗
        fclose($fp);
        HakoError::lockFail();

        return false;
    }

    /**
     * ファイルをアンロックする
     * @param  [type] $fp file pointer
     * @return void
     */
    public static function unlock($fp): void
    {
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * アラートタグを出力する
     * @param  string $message 本文
     * @param  string $status  アラート種類："success","info","warning","danger".
     * @return void
     */
    public static function makeTagMessage($message, $status = 'success'): void
    {
        echo '<div class="alert alert-'.$status.'" role="alert">';
        echo nl2br($message, false);
        echo '</div>';
    }

    /**
     * ランダムな文字列を返す
     * @param  integer $length [description]
     * @return [type]       [description]
     */
    public static function random_str(int $length = 8): string
    {
        static $seeds;

        if (!$seeds) {
            $seeds = array_flip(array_merge(range("a", "z"), range("A", "Z"), range("0", "9")));
        }
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= array_rand($seeds);
        }

        return $str;
    }

    /**
     * 指定座標が島サイズ内か判定する
     * @param integer $x
     * @param integer $y
     * @return boolean
     */
    public static function isInnerLand(int $x, int $y): bool
    {
        global $init;

        return -1 < $x && $x < $init->islandSize && -1 < $y && $y < $init->islandSize;
    }

    /**
     * $catに対応した計算式で数値を返す
     * [TODO] 関数を他所に切り出す
     * @param  string $cat 計算したい対象（あらかじめ定義しておく）
     * @param  array  $p   プレイヤーデータ
     * @param  array  $mod 変数の上書き（あれば）
     * @return float       計算結果
     */
    public static function calc(string $cat, array $p, array $mod = []): float
    {
        $opt = [];
        foreach ($mod as $k => $v) {
            if (array_key_exists($k, $p)) {
                $p[$k] = $v;
            } else {
                $opt[$k] = $v;
            }
        }
        unset($mod, $k, $v);

        if ($cat === 'unemployed') {
            return ($p['pop'] - ($p['farm'] + $p['factory'] + $p['commerce'] + $p['mountain'] + $p['hatuden']) * 10) / $p['pop'] * 100;
        }
        /**
         * 【電力消費量】
         * 「人口が農業枠未満」か「Σ(工業,商業,採掘場枠)がゼロ」→ ゼロ
         * => 「人口-農業枠」か「工業枠*3/2 ＋ 商業枠 ＋ 採掘場枠/2」の小さい方
         */
        if ($cat === 'power_consumption') {
            $civil_without_farmer = $p['pop'] - ($p['farm'] * 10);
            $not_have_industry = max($p['factory'], $p['commerce'], $p['mountain']) < 1;
            if ($civil_without_farmer < 1 || $not_have_industry) {
                unset($civil_without_farmer);

                return 0;
            }
            unset($is_civil_farmer_all, $not_have_industry);

            return min($civil_without_farmer, 10 * ($p['factory']*3/2 + $p['commerce'] + $p['mountain']/2));
        }
        /**
         * 【電力発電量】
         */
        if ($cat === 'power_supply') {
            return $p["hatuden"] * 10;
        }
        /**
         * 【電力供給率】
         */
        if ($cat === 'power_supply_rate') {
            $pc = self::calc('power_consumption', $p);
            $pc = $pc !== 0.0 ? $pc : INF;

            return self::calc("power_supply", $p) / $pc;
        }
        if ($cat === 'power_supply_rate_1') {
            $pc = self::calc('power_consumption', $p);
            $pc = $pc !== 0.0 ? $pc : INF;

            return min(1.0, self::calc("power_supply", $p) / $pc);
        }
        /**
         * 【総合ポイント】
         * 「人口ゼロかBF」→ 0
         * => 10*(15人口 + 資金 + 食料 + 2農業 + 工業 + 1.2商業 + 2採掘 + 3発電 + サッカー + 5土地 + 5討伐 + 10装弾 + 5怪獣)
         */
        if ($cat === 'grand_point') {
            if ($p['pop'] == 0 || $p['isBF'] == 1) {
                return 0;
            }

            return 10 * ($p['pop']*15 + $p['money'] + $p['food'] + $p['farm']*2
                + $p['factory'] + $p['commerce']*1.2 + $p['mountain']*2
                + $p['hatuden']*3 + $p['team'] + $p['area']*5 + $p['taiji']*5
                + $p['fire']*10 + $p['monster']*5);
        }

        switch ($cat) {
            case 'enesyouhi':
                return round(($p['pop']/100) + ($p['factory']*2/3) + ($p['commerce']/3) + ($p['mountain']/4));

            case 'ene':
                return round($p['hatuden'] / Util::calc('enesyouhi', $p) * 100);
        }

        throw new InvalidArgumentException("Parameter `{$cat}` is not defined. maybe wrong.");
    }

    /**
     * 各種イベントが発生するかどうかの判定
     * @param  string $cat イベント名称（あらかじめ定義しておく）
     * @return bool        発生するか（したか）
     */
    public static function event_flag(string $cat): bool
    {
        global $init;

        if ($cat === 'blackout') {
            return Util::random(1000) < $init->disTenki;
        }

        throw new InvalidArgumentException('Parameter ' . $cat . ' is not defined. maybe wrong.');
    }

    /**
     * WIP
     * @param  boolean $withGet [description]
     * @return [type]           [description]
     */
    public static function parsePostData($withGet=false)
    {
        global $init;

        $mode = $_POST['mode'] ?? '';
    }

    /**
     * 文字のエスケープ処理
     * @param  string  $s    任意の入力文字列
     * @param  integer $mode boolキャスト：nl2brの有無（複数改行の圧縮機能あり）
     * @return string        キャスト済み文字列
     */
    public static function htmlEscape($s, $mode = 0): string
    {
        $s = preg_replace('/&amp;(?=#[\d;])/', '&', htmlspecialchars($s, ENT_QUOTES, 'UTF-8'));

        if ($mode) {
            $s = strtr($s, array_fill_keys(["\r\n", "\r", "\n"], '<br>'));
            $s = preg_replace('/(<br>){3,}/g', '<br><br>', $s); // 大量改行対策
        }

        return $s;
    }

    /**
     * 文字列$strが$prefixから始まるかどうか
     * @param  string $str    検索対象
     * @param  mixed  $prefix 検索したい文字列・文字列の配列
     * @return bool
     */
    public static function starts_with(string $str, $prefix): bool
    {
        $type = gettype($prefix);
        switch ($type) {
            case "string":
                return mb_substr($str, 0, mb_strlen($prefix)) === $prefix;
            case "array":
                foreach ($prefix as $p) {
                    if (mb_substr($str, 0, mb_strlen($p)) === $p) {
                        return true;
                    }
                }

                return false;
        }

        throw new \InvalidArgumentException("Arguments #1 require type of `String[] | String` (Actual `{$type}`)");
    }



    public static function get_anonymous_usage_stats(string $path = "")
    {
        $outtype_flg = is_dir($path);
        $fileIO = (new class {
            use \Hakoniwa\Model\FileIO;
        });

        ob_start();
        phpinfo();
        $_phpinfo = ob_get_contents();
        ob_end_clean();

        // [TODO] これいる？
        // if (strpos($_phpinfo, "phpinfo() has been disabled for security reasons") ===false) {
        //     $_phpinfo = "";
        // }

        if ($outtype_flg) {
            $path = $path.DS.self::random_str();
            $fileIO->mkfile($path."/phpinfo.html", $_phpinfo);
        }

        return $outtype_flg ? $path : ["phpinfo" => $_phpinfo];
    }
}



function println(...$strs): void
{
    foreach ($strs as $str) {
        echo $str;
    }
    echo PHP_EOL;
}
