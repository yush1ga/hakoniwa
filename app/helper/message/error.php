<?php
/**
 * 箱庭諸島 S.E
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

class HakoError
{
    public static function wrongPassword(): void
    {
        Util::makeTagMessage("パスワードが違います", "danger");
    }

    public static function wrongID(): void
    {
        Util::makeTagMessage("IDが違います", "danger");
    }

    // hakojima.datがない
    public static function noDataFile(): void
    {
        Util::makeTagMessage("データファイルが開けません", "danger");
    }

    public static function newIslandFull(): void
    {
        Util::makeTagMessage("申し訳ありません、島が一杯で登録できません！！", "danger");
    }

    public static function newIslandNoName(): void
    {
        Util::makeTagMessage("島につける名前が必要です", "danger");
    }

    public static function newIslandBadName(): void
    {
        Util::makeTagMessage(",?()&lt;&gt;\$とか入ってたり、変な名前はやめましょう", "danger");
    }

    public static function newIslandAlready(): void
    {
        Util::makeTagMessage("その島ならすでに発見されています", "danger");
    }

    public static function newIslandNoPassword(): void
    {
        Util::makeTagMessage("パスワードが必要です", "danger");
    }

    public static function changeNoMoney(): void
    {
        Util::makeTagMessage("資金不足のため変更できません", "danger");
    }

    public static function changeNothing(): void
    {
        Util::makeTagMessage("名前、パスワードともに空欄です", "danger");
    }

    public static function problem(): void
    {
        Util::makeTagMessage("問題が発生しました", "danger");
    }

    public static function lockFail(): void
    {
        Util::makeTagMessage("同時アクセスエラーです。\nブラウザの「戻る」ボタンを押し、しばらく待ってから再度お試し下さい", "danger");
    }

    public static function wrongMasterPassword(): void
    {
        Util::makeTagMessage("マスタパスワードが入力されていないか間違っています", "danger");
    }

    public static function wrongSpecialPassword(): void
    {
        Util::makeTagMessage("特殊パスワードが入力されていないか間違っています", "danger");
    }
    public static function necessaryBeSetAnotherPassword(): void
    {
        \Util::makeTagMessage('マスタパスワードと特殊パスワードには、別のフレーズを入力してください', 'danger');
    }

    public function __destruct()
    {
        HTML::footer();
        exit;
    }

    /**
     * 同盟
     */
    // すでにその名前の同盟がある場合
    public static function newAllyAlready(): void
    {
        Util::makeTagMessage("その同盟は既に結成されています", "danger");
    }
    // すでにそのマークの同盟がある場合
    public static function markAllyAlready(): void
    {
        Util::makeTagMessage("そのマークは既に使用されています", "danger");
    }
    // 別の同盟を結成している
    public static function leaderAlready(): void
    {
        Util::makeTagMessage("盟主は、自分の同盟以外には加盟できません", "danger");
    }
    // 別の同盟に加盟している
    public static function otherAlready(): void
    {
        Util::makeTagMessage("ひとつの同盟にしか加盟できません", "danger");
    }
    // 資金足りず
    public static function noMoney(): void
    {
        Util::makeTagMessage("資金不足です", "danger");
    }
    // IDチェックにひっかかる
    public static function wrongAlly(): void
    {
        Util::makeTagMessage("盟主としての権限がありません", "danger");
    }
    // 新規で同盟がない場合
    public static function newAllyNoName(): void
    {
        Util::makeTagMessage("同盟につける名前が必要です", "danger");
    }
    // 管理者以外結成不可
    public static function newAllyForbbiden(): void
    {
        Util::makeTagMessage("現在、受付を中止しています", "warning");
    }
}
