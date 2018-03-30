<?php
/**
 * 箱庭諸島 S.E - 各種ユーティリティ定義用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */
class Util
{

    /**
     * 資金を丸めて表示する
     * @param  integer $money 資金額
     * @return string         丸めた文字列
     */
    public static function aboutMoney(int $money = 0):string
    {
        global $init;
        $digit = (int)$init->moneyMode;

        return ($digit <= 0)? $money .$init->unitMoney
                : ($money < $digit)? "推定{$digit}{$init->unitMoney}未満"
                : '推定'. round($money / $digit) * $digit . $init->unitMoney;
    }

    /**
     * 経験値からミサイル基地レベルを算出
     * @param  [type]  $kind ミサイル基地種別
     * @param  integer $exp  経験値
     * @return integer       対応した基地レベル値
     */
    public static function expToLevel(int $kind, int $exp):int
    {
        global $init;

        // ミサイル基地
        if ($kind == $init->landBase) {
            for ($i = $init->maxBaseLevel; $i > 1; $i--) {
                if ($exp >= $init->baseLevelUp[$i - 2]) {
                    return $i;
                }
            }

            return 1;
        // 海底基地
        } else {
            for ($i = $init->maxSBaseLevel; $i > 1; $i--) {
                if ($exp >= $init->sBaseLevelUp[$i - 2]) {
                    return $i;
                }
            }

            return 1;
        }
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

        return ['kind' => $kind, 'name' => $name, 'hp' => $hp];
    }

    /**
     * 島の名前から番号を算出
     * @param  [type] $hako [description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function nameToNumber($hako, $name)
    {
        // 全島から探す
        for ($i = 0; $i < $hako->islandNumber; $i++) {
            if (strcmp($name, $hako->islands[$i]['name']) == 0) {
                return $i;
            }
        }
        // 見つからなかった場合
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
            $name .= '<span style="color:'.$color.'";font-weight:bold;>' . $mark . '</span> ';
        }
        $name .= $island['name'] . "島";

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

        // nullチェック
        if (empty($p2)) {
            return false;
        }
        if (!file_exists($init->passwordFile)) {
            HakoError::probrem();

            return false;
        }
        $fp = fopen($init->passwordFile, "r");
        $masterPasswd = chop(fgets($fp, READ_LINE));
        fclose($fp);

        // マスターパスワードチェック
        if (password_verify($p2, $masterPasswd)) {
            return true;
        }

        // 通常のパスワードチェック
        $isLegacyHash = '$2y$10$' !== substr($p1, 0, 7);
        if (!$isLegacyHash) {
            return password_verify($p2, $p1);
        }
        if (strcmp($p1, Util::encode($p2)) == 0) {
            return true;
        }

        return false;
    }

    /**
     * 特殊パスワードチェック
     * @param  string $p 入力パスワード
     * @return bool
     */
    public static function checkSpecialPassword(string $p = ""): bool
    {
        global $init;

        // nullチェック
        if (empty($p)) {
            return false;
        }
        if (!file_exists($init->passwordFile)) {
            HakoError::probrem();

            return false;
        }
        $fp = fopen($init->passwordFile, "r");
        $specialPasswd = chop(fgets($fp, READ_LINE));//1行目を破棄
        $specialPasswd = chop(fgets($fp, READ_LINE));
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
    public static function slideFront(&$command, $number = 0)
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
    public static function slideBack(&$command, $number = 0)
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
            usleep((LOCK_RETRY_INTERVAL - mt_rand(0, 300)) * 1000);
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
    public static function unlock($fp)
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
    public static function makeTagMessage($message, $status = 'success')
    {
        echo '<div class="alert alert-'.$status.'" role="alert">';
        echo nl2br($message, false);
        echo '</div>';
    }

    /**
     * ランダムな文字列を返す
     * @param  integer $max [description]
     * @return [type]       [description]
     */
    public static function rand_string(int $max = 32): string
    {
        return substr(md5(uniqid(rand_number(), true)), 0, $max);
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
     * @param  string $cat [description]
     * @param  array  $isl [description]
     * @return float       [description]
     */
    public static function calcIslandData(string $cat, array $isl): float
    {
        switch ($cat) {
            case 'unemployed':
                return ($isl['pop'] - ($isl['farm'] + $isl['factory'] + $isl['commerce'] + $isl['mountain'] + $isl['hatuden']) * 10) / $isl['pop'] * 100;
            case 'enesyouhi':
                return round(($isl['pop']/100) + ($isl['factory']*2/3) + ($isl['commerce']/3) + ($isl['mountain']/4));
            case 'ene':
                return round($isl['hatuden'] / Util::calcIslandData('enesyouhi', $isl) * 100);
        }
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
}



function println(...$strs)
{
    foreach ($strs as $str) {
        echo $str;
    }
    echo PHP_EOL;
}

function h(string $str): string
{
    return preg_replace('/&amp;(?=#[\d;])/', '&', htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}
