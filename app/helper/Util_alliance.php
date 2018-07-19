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
     * 人口順にソート（同盟バージョン）
     * @param       &$game
     * @return void
     */
    public static function allySort(&$game)
    {
        /**
         * 人口を比較、同盟一覧用
         * @return integer  $xのほうが大きければ-1、$yのほうが大きければ1
         */
        function comp($x, $y)
        {
            if (($x['dead'] ?? 0) == 1) {
                return +1;
            }
            if (($y['dead'] ?? 0) == 1) {
                return -1;
            }
            // mean ($x['score'] > $y['score']) ? -1 : 1;
            return $y['score'] <=> $x['score'];
        }

        if (count($game->ally) > 1) {
            usort($game->ally, 'comp');
        }
    }

    /**
     * 同盟の名前からIDを得る
     * @param  [type] $hako [description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
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

    /**
     * 同盟のマークからIDを得る
     * @param         $hako ゲーム総合データ
     * @param  string $mark
     * @return [type]       [description]
     */
    public static function aMarkToId($hako, $mark)
    {
        // 全島から探す
        for ($i = 0; $i < $hako->allyNumber; $i++) {
            if ($hako->ally[$i]['mark'] === $mark) {
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
