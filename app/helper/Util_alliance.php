<?php
namespace Hakoniwa\Helper;

require_once 'util.php';
// use Util;
/**
 *
 */
class Util_alliance extends \Util
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
