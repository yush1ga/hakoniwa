<?php
namespace Hakoniwa\Helper;

use Util;
/**
 *
 */
class Util_alliance extends Util
{
    /**
     * 同盟の占有率の計算
     */
    public static function calculates_share(&$hako)
    {
        $total_score = 0;

        for ($i = 0; $i < $hako->allyNumber; $i++) {
            $total_score += $hako->ally[$i]['score'];
        }

        for ($i = 0; $i < $hako->allyNumber; $i++) {
            if ($total_score != 0) {
                $hako->ally[$i]['occupation'] = intdiv($hako->ally[$i]['score'], $total_score * 100);
            } else {
                $hako->ally[$i]['occupation'] = intdiv(100, $hako->allyNumber);
            }
        }

        return;
    }

    //---------------------------------------------------
    // 人口順にソート(同盟バージョン)
    //---------------------------------------------------

    public static function allySort(&$hako)
    {
        /**
         * 人口を比較、同盟一覧用
         * @return integer  $xのほうが大きければ-1、$yのほうが大きければ1
         */
        function comparator($x, $y)
        {
            if ($x['dead'] ?? 0 == 1) {
                return +1;
            }
            if ($y['dead'] ?? 0 == 1) {
                return -1;
            }
            // mean ($x['score'] > $y['score']) ? -1 : 1;
            return $y['score'] <=> $x['score'];
        }

        usort($hako->ally, 'comparator');
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

    //---------------------------------------------------
    // 同盟の名前からIDを得る
    //---------------------------------------------------
    public static function aNameToId($hako, $name)
    {
        // 全島から探す
        for ($i = 0; $i < $hako->allyNumber; $i++) {
            if ($hako->ally[$i]['name'] == $name) {
                return $hako->ally[$i]['id'];
            }
        }
        // 見つからなかった場合
        return -1;
    }

    //---------------------------------------------------
    // 同盟のマークからIDを得る
    //---------------------------------------------------
    public static function aMarkToId($hako, $mark)
    {
        // 全島から探す
        for ($i = 0; $i < $hako->allyNumber; $i++) {
            if ($hako->ally[$i]['mark'] == $mark) {
                return $hako->ally[$i]['id'];
            }
        }
        // 見つからなかった場合
        return -1;
    }

    /**
     * 文字のエスケープ処理
     * @param  string  $s    任意の入力文字列
     * @param  integer $mode boolキャスト：nl2brの有無（複数改行の圧縮機能あり）
     * @return string        キャスト済み文字列
     */
    public static function htmlEscape($s, $mode = 0)
    {
        $s = h($s);

        if ($mode) {
            $s = strtr($s, array_fill_keys(["\r\n", "\r", "\n"], '<br>'));
            $s = preg_replace('/(<br>){3,}/g', '<br><br>', $s); // 大量改行対策
        }

        return $s;
    }

    /**
     * ファイルをロックする（書き込み時）
     */
    public static function lock_on_write($fp)
    {
        set_file_buffer($fp, 0);
        if (!flock($fp, LOCK_EX)) {
            HakoError::lockFail();
        }
        rewind($fp);
    }

    /**
     * ファイルをロックする（読み込み時）
     */
    public static function lock_on_read($fp)
    {
        set_file_buffer($fp, 0);
        if (!flock($fp, LOCK_SH)) {
            HakoError::lockFail();
        }
        rewind($fp);
    }
}
