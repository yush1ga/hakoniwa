<?php
/**
 * 箱庭諸島 S.E
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

class Success
{
    public static function comment()
    {
        Util::makeTagMessage("コメントを更新しました", "success");
    }

    public static function change()
    {
        Util::makeTagMessage("変更完了しました", "success");
    }

    // コマンド削除
    public static function commandDelete()
    {
        Util::makeTagMessage("コマンドを削除しました", "success");
    }

    // コマンド登録
    public static function commandAdd()
    {
        Util::makeTagMessage("コマンドを登録しました", "success");
    }

    // 島の強制削除
    public static function deleteIsland($name, $init)
    {
        Util::makeTagMessage("{$name}{$init->nameSuffix}を強制削除しました", "success");
    }

    // 盟主コメント変更完了
    public static function allyPactOK($name)
    {
        Util::makeTagMessage("{$name}のコメントを変更しました", "success");
    }
    // 同盟データの再構成
    public static function allyDataUp()
    {
        Util::makeTagMessage("同盟データを再構成しました（ターン更新後に反映されます）", "info");
    }

    public static function standard()
    {
        Util::makeTagMessage("成功しました", "success");
    }
}
