<?php

declare(strict_types=1);

use \PHPUnit\Framework\TestCase;

final class AllianceTest extends TestCase
{
    public function testTestAlliance(): void
    {
        $this->Alliance = new \Hakoniwa\Model\Alliance;
    }

    public function testWithdrawalAlliance(): void
    {
        /**
         * ** 同盟の脱退に必要な入力
         * ・whoami
         * ・パスワード
         * ・どの同盟を抜けるのか
         *
         * ** 必要なバリデーション
         * ・ユーザーが存在するか
         * ・パスワードが管理者のものか
         * ・パスワードが正しいか
         * ・同盟が存在するか
         * ・抜けられる同盟か（盟主は抜けられない：解散のみ）
         * ・・仕様？
         * ・脱退費を払えるか
         *
         * ** 必要な処理
         * ・ユーザーデータから当該の同盟に関する情報を消す
         * ・同盟データから当該のユーザーに関する情報を消す
         * ・ユーザーデータを書き戻す
         * ・同盟データを書き戻す
         * ・同盟データを更新する
         */
    }

    public function testUserValidation($game = [], $data = ["Whoami" => 2]): void
    {
        $this->assertEquals("1", $data["Whoami"]);
    }
}
