<?php

namespace Hakoniwa\Model;

// require_once __DIR__.'/../../vendor/autoload.php';
require_once HELPERPATH.'Util_alliance.php';

use \Hakoniwa\Helper\Util_alliance as Util;

/**
 * Alliances Model
 * @author @Sotalbireo
 */
class Alliance
{
    private const MAX_ALLIANCE_ID = 200;


    /**
     * 同盟登録確認
     * -[] 島名とパスワードの対応
     * -[] 記章重複
     * -[] 色バリデート
     * -[] 名前バリデート
     * -[x] 資金不足（設定時のみ）
     * -[x] 多重登録（設定時のみ）
     * -[] 管理者判定（設定時のみ）
     * @param  [type] $game [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function confirm($game, $data)
    {
        global $init;

        $data['Password'] = base64_decode($data['Password'], true);

        $island_ID = (int)$data['Whoami'];
        $password = base64_decode($data['Password'] ?? '', true);
        $island = $game->islands[(int)$game->idToNumber[$island_ID]];
        $candidate = [
            'name'  => htmlspecialchars($data['AllianceName']),
            'sign'  => (int)$data['AllianceSign'],
            'color' => $data['AllianceColor']
        ];

        $admin_mode = Util::checkPassword("", $password);
        $checked = [
            'id' =>    ['status' => true, 'message' => ''],
            'pass' =>  ['status' => true, 'message' => ''],
            'name' =>  ['status' => true, 'message' => ''],
            'sign' =>  ['status' => true, 'message' => ''],
            'color' => ['status' => true, 'message' => ''],
            'other' => ['status' => true, 'messages' => []],
            'temp' => ''
        ];

        if ($init->allyJoinOne && count($island['allyId']) > 0) {
            $checked['other']['status'] = false;
            $checked['other']['messages'][] = 'reach_join_limit';
        }

        if ($admin_mode) {/* thru */
        } else if (!Util::checkPassword($island['password'], $password)) {
            // HakoError::wrongPassword();

            $checked['pass']['status'] = false;
            $checked['pass']['message'] = 'wrong_password';
        }

        if ($admin_mode) {/* thru */
        } else if ($island['money'] < $init->costMakeAlly) {
            $checked['other']['status'] = false;
            $checked['other']['messages'][] = 'not_enough_money';
        }

        header('Content-Type:application/json;charset=utf-8');
        echo json_encode($checked);
    }



    /**
     * 結成
     * @param  [type] $hako [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function register($hako, $data, $dry_run = false)
    {
        global $init;

        // $current_ID = $data['ISLANDID'];
        $user_ID = $data['Whoami'];
        $password = $data['Password'];
        $candidate = [
            'name'  => htmlspecialchars($data['AllianceName']),
            'sign'  => $data['AllianceSign'],
            'color' => $data['AllianceColor']
        ];
        // $allyName = ;
        // $allyMark = ;
        // $allyColor = ;
        $admin_mode = false;

        // パスワードチェック
        if (Util::checkPassword("", $password)) {
            $admin_mode = true;

            if ($allyID > MAX_ALLIANCE_ID) {
                $max = $allyID;
                if ($hako->allyNumber) {
                    for ($i = 0; $i < count($hako->ally); $i++) {
                        if ($max <= $hako->ally[$i]['id']) {
                            $max = $hako->ally[$i]['id'] + 1;
                        }
                    }
                }
                $current_ID = $max;
            } else {
                $current_ID = $hako->ally[$current_alliance_number]['id'];
            }
        }

        $island = $hako->islands[$user_ID];
        if (!$admin_mode
            && !Util::checkPassword($island['password'], $data['Password'])) {
            HakoError::wrongPassword();

            return;
        }


        if (!$init->allyUse && !$admin_mode) {
            HakoError::newAllyForbbiden();

            return;
        }

        function is_match_in_array($string, $array)
        {
            foreach ($array as $v) {
                if (strpos($string, $v) !== false) {
                    return true;
                }
            }

            return false;
        }

        // 同盟名が正当かチェック
        if ($candidate['name'] === ''
            || preg_match($init->regex_denying_name_words, $candidate['name'])
            || is_match_in_array($candidate['name'], $init->denying_name_words)) {
            HakoError::newIslandBadName();

            return;
        }

        $this->check_duplicate();

        // 結成資金不足判定
        if ($admin_mode) {/* thru */
        } elseif ($island['money'] < $init->costMakeAlly) {
            HakoError::noMoney();

            return;
        }

        $n = $hako->idToAllyNumber[$current_ID] ?? '';
        if ($n !== '') {
            if ($admin_mode && ($allyID != '') && ($allyID < 200)) {
                $alliance_member = $hako->ally[$n]['memberId'];
                $aIsland = $hako->islands[$hako->idToNumber[$allyID]];
                $flag = 0;
                foreach ($alliance_member as $id) {
                    if ($id == $allyID) {
                        $flag = 1;

                        break;
                    }
                }
                if (!$flag) {
                    if ($aIsland['allyId'][0] == '') {
                        $flag = 2;
                    }
                }
                if (!$flag) {
                    echo "変更できません。\n";

                    return;
                }
                $hako->ally[$n]['id']    = $allyID;
                $hako->ally[$n]['oName'] = $aIsland['name'];
                if ($flag == 2) {
                    $hako->ally[$n]['password'] = $aIsland['password'];
                    $hako->ally[$n]['score']    = $aIsland['pop'];
                    $hako->ally[$n]['number'] ++;
                    array_push($hako->ally[$n]['memberId'], $aIsland['id']);
                    array_push($aIsland['allyId'], $aIsland['id']);
                }
            } else {
                // すでに結成ずみなら変更
            }
        } else {
            // 他の島の同盟に入っている場合は、結成できない
            $flag = 0;
            for ($i = 0; $i < $hako->allyNumber; $i++) {
                $alliance_member = $hako->ally[$i]['memberId'];
                foreach ($alliance_member as $id) {
                    if ($id == $current_ID) {
                        $flag = 1;

                        break;
                    }
                }
                if ($flag) {
                    break;
                }
            }
            if ($flag) {
                HakoError::otherAlready();

                return;
            }
            if (($init->allyUse == 2) && !$admin_mode && !AllyUtil::checkPassword("", $data['PASSWORD'])) {
                HakoError::newAllyForbbiden();

                return;
            }
            // 新規
            $n = $hako->allyNumber;
            $hako->ally[$n]['id'] = $current_ID;
            $memberId = [];
            if ($allyID < 200) {
                $hako->ally[$n]['oName']    = $island['name'].$init->nameSuffix;
                $hako->ally[$n]['password'] = $island['password'];
                $hako->ally[$n]['number']   = 1;
                $memberId[0]                = $current_ID;
                $hako->ally[$n]['score']    = $island['pop'];
            } else {
                $hako->ally[$n]['oName']    = '';
                $hako->ally[$n]['password'] = AllyUtil::encode($data['PASSWORD']);
                $hako->ally[$n]['number']   = 0;
                $hako->ally[$n]['score']    = 0;
            }
            $hako->ally[$n]['occupation']   = 0;
            $hako->ally[$n]['memberId']     = $memberId;
            $island['allyId']               = $memberId;
            $ext = [0];
            $hako->ally[$n]['ext']          = $ext;
            $hako->idToAllyNumber[$current_ID] = $n;
            $hako->allyNumber++;
        }

        // 同盟の各種の値を設定
        $hako->ally[$n]['name']  = $allyName;
        $hako->ally[$n]['mark']  = $allyMark;
        $hako->ally[$n]['color'] = $allyColor;

        // 費用をいただく
        $island['money'] -= !$admin_mode ? $init->costMakeAlly : 0;

        // データ格納先へ
        $hako->islands[$current_number] = $island;

        // データ書き出し
        Util::calculates_share($hako);
        Util::allySort($hako);
        $hako->writeAllyFile();

        // トップへ
        $html = new HtmlAlly();
        $html->allyTop($hako, $data);
    }

    /**
     * 変更
     * @param  [type] $hako [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function update($hako, $data)
    {
        global $init;

        $current_ID = $data['ISLANDID'];
        $allyID = $data['ALLYID'] ?? "";
        $current_alliance_number = $data['ALLYNUMBER'] ?? "";
        $allyName = htmlspecialchars($data['ALLYNAME']);
        $allyMark = $data['MARK'];
        $allyColor = $data['colorCode'];
        $admin_mode = 0;

        // パスワードチェック
        $data['OLDPASS'] = $data['OLDPASS'] ?? "";
        if (AllyUtil::checkPassword("", $data['OLDPASS'])) {
            $admin_mode = 1;
            if ($allyID > 200) {
                $max = $allyID;
                if ($hako->allyNumber) {
                    for ($i=0; $i < count($hako->ally); $i++) {
                        if ($max <= $hako->ally[$i]['id']) {
                            $max = $hako->ally[$i]['id'] + 1;
                        }
                    }
                }
                $current_ID = $max;
            } else {
                $current_ID = $hako->ally[$current_alliance_number]['id'];
            }
        }
        if (!$init->allyUse && !$admin_mode) {
            HakoError::newAllyForbbiden();

            return;
        }
        // 同盟名があるかチェック
        if ($allyName == '') {
            HakoError::newAllyNoName();

            return;
        }
        // 同盟名が正当かチェック
        if (preg_match("/[,\?\(\)\<\>\$]|^無人|^沈没$/", $allyName)) {
            HakoError::newIslandBadName();

            return;
        }
        // 名前の重複チェック
        $current_number = $hako->idToNumber[$current_ID];
        if (!($admin_mode && ($allyID == '') && ($allyID < 200)) &&
            ((AllyUtil::nameToNumber($hako, $allyName) != -1) ||
            ((AllyUtil::aNameToId($hako, $allyName) != -1) && (AllyUtil::aNameToId($hako, $allyName) != $current_ID)))) {
            HakoError::newAllyAlready();

            return;
        }
        // マークの重複チェック
        if (!($admin_mode && ($allyID == '') && ($allyID < 200)) &&
            ((AllyUtil::aMarkToId($hako, $allyMark) != -1) && (AllyUtil::aMarkToId($hako, $allyMark) != $current_ID))) {
            HakoError::markAllyAlready();

            return;
        }
        // passwordの判定
        $island = $hako->islands[$current_number];
        if (!$admin_mode && !AllyUtil::checkPassword($island['password'], $data['PASSWORD'])) {
            HakoError::wrongPassword();

            return;
        }
        // 結成資金不足判定
        if (!$admin_mode && $island['money'] < $init->costMakeAlly) {
            HakoError::noMoney();

            return;
        }
        $n = $hako->idToAllyNumber[$current_ID] ?? '';
        if ($n !== '') {
            if ($admin_mode && ($allyID != '') && ($allyID < 200)) {
                $alliance_member = $hako->ally[$n]['memberId'];
                $aIsland = $hako->islands[$hako->idToNumber[$allyID]];
                $flag = 0;
                foreach ($alliance_member as $id) {
                    if ($id == $allyID) {
                        $flag = 1;

                        break;
                    }
                }
                if (!$flag) {
                    if ($aIsland['allyId'][0] == '') {
                        $flag = 2;
                    }
                }
                if (!$flag) {
                    echo "変更できません。\n";

                    return;
                }
                $hako->ally[$n]['id']    = $allyID;
                $hako->ally[$n]['oName'] = $aIsland['name'];
                if ($flag == 2) {
                    $hako->ally[$n]['password'] = $aIsland['password'];
                    $hako->ally[$n]['score']    = $aIsland['pop'];
                    $hako->ally[$n]['number'] ++;
                    array_push($hako->ally[$n]['memberId'], $aIsland['id']);
                    array_push($aIsland['allyId'], $aIsland['id']);
                }
            } else {
                // すでに結成ずみなら変更
            }
        } else {
            // 他の島の同盟に入っている場合は、結成できない
            $flag = 0;
            for ($i = 0; $i < $hako->allyNumber; $i++) {
                $alliance_member = $hako->ally[$i]['memberId'];
                foreach ($alliance_member as $id) {
                    if ($id == $current_ID) {
                        $flag = 1;

                        break;
                    }
                }
                if ($flag) {
                    break;
                }
            }
            if ($flag) {
                HakoError::otherAlready();

                return;
            }
            if (($init->allyUse == 2) && !$admin_mode && !AllyUtil::checkPassword("", $data['PASSWORD'])) {
                HakoError::newAllyForbbiden();

                return;
            }
            // 新規
            $n = $hako->allyNumber;
            $hako->ally[$n]['id'] = $current_ID;
            $memberId = [];
            if ($allyID < 200) {
                $hako->ally[$n]['oName']    = $island['name'].$init->nameSuffix;
                $hako->ally[$n]['password'] = $island['password'];
                $hako->ally[$n]['number']   = 1;
                $memberId[0]                = $current_ID;
                $hako->ally[$n]['score']    = $island['pop'];
            } else {
                $hako->ally[$n]['oName']    = '';
                $hako->ally[$n]['password'] = AllyUtil::encode($data['PASSWORD']);
                $hako->ally[$n]['number']   = 0;
                $hako->ally[$n]['score']    = 0;
            }
            $hako->ally[$n]['occupation']   = 0;
            $hako->ally[$n]['memberId']     = $memberId;
            $island['allyId']               = $memberId;
            $ext = [0];
            $hako->ally[$n]['ext']          = $ext;
            $hako->idToAllyNumber[$current_ID] = $n;
            $hako->allyNumber++;
        }

        // 同盟の各種の値を設定
        $hako->ally[$n]['name']  = $allyName;
        $hako->ally[$n]['mark']  = $allyMark;
        $hako->ally[$n]['color'] = $allyColor;

        // 費用をいただく
        $island['money'] -= !$admin_mode ? $init->costMakeAlly : 0;

        // データ格納先へ
        $hako->islands[$current_number] = $island;

        // データ書き出し
        AllyUtil::calculates_share($hako);
        AllyUtil::allySort($hako);
        $hako->writeAllyFile();

        // トップへ
        $html = new HtmlAlly();
        $html->allyTop($hako, $data);
    }

    /**
     * 同盟の解散（削除）
     * @param         $hako ゲーム総合データ
     * @param         $data プレイヤー入力
     * @return [type]       [description]
     */
    public function delete_alliance($hako, $data)
    {
        global $init;

        $current_id = $data['ISLANDID'];
        $current_alliance_number = $data['ALLYNUMBER'];
        $current_number = $hako->idToNumber[$current_id];
        $island = $hako->islands[$current_number];
        $n = $hako->idToAllyNumber[$current_id];
        $admin_mode = 0;

        // 認証
        if (Util::checkPassword("", ($data['OLDPASS'] ?? ''))) {
            // 管理者
            $n = $current_alliance_number;
            $current_id = $hako->ally[$n]['id'];
            $admin_mode = 1;
        } else {
            // 一般

            // 島パスワード
            if (!Util::checkPassword($island['password'], $data['PASSWORD'])) {
                HakoError::wrongPassword();

                return;
            }
            // 同盟パスワード
            if (!Util::checkPassword($hako->ally[$n]['password'], $data['PASSWORD'])) {
                HakoError::wrongPassword();

                return;
            }
            // 念のためIDもチェック
            if ($hako->ally[$n]['id'] != $current_id) {
                HakoError::wrongAlly();

                return;
            }
        }

        $alliance_member = $hako->ally[$n]['memberId'];

        // 管理者 かつ （同盟に在籍者がいる または 同盟がない）
        if ($admin_mode && (($alliance_member[0] != '') || ($n == ''))) {
            echo "削除できません。\n";

            return;
        }

        foreach ($alliance_member as $id) {
            $island = $hako->islands[$hako->idToNumber[$id]];
            $new_id = [];
            foreach ($island['allyId'] as $id) {
                if ($id != $current_id) {
                    array_push($new_id, $id);
                }
            }
            $island['allyId'] = $new_id;
        }
        $hako->ally[$n]['dead'] = 1;
        $hako->idToAllyNumber[$current_id] = '';
        $hako->allyNumber--;

        // データ格納先へ
        $hako->islands[$current_number] = $island;

        // データ書き出し
        Util::calculates_share($hako);
        Util::allySort($hako);
        $hako->writeAllyFile();

        // トップへ
        $html = new HtmlAlly();
        $html->allyTop($hako, $data);
    }

    //--------------------------------------------------
    // 加盟・脱退
    //--------------------------------------------------
    public function joinAllyMain($hako, $data)
    {
        global $init;

        $current_ID = $data['ISLANDID'];
        $current_alliance_number = $data['ALLYNUMBER'];
        $current_number = $hako->idToNumber[$current_ID];
        $island = $hako->islands[$current_number];

        // パスワードチェック
        if (!(AllyUtil::checkPassword($island['password'], $data['PASSWORD']))) {
            // password間違い
            HakoError::wrongPassword();

            return;
        }

        // 盟主チェック
        if ($hako->idToAllyNumber[$current_ID]) {
            HakoError::leaderAlready();

            return;
        }
        // 複数加盟チェック
        $ally = $hako->ally[$current_alliance_number];
        if ($init->allyJoinOne && ($island['allyId'][0] != '') && ($island['allyId'][0] != $ally['id'])) {
            HakoError::otherAlready();

            return;
        }

        $alliance_member = $ally['memberId'];
        $newAllyMember = [];
        $flag = 0;

        foreach ($alliance_member as $id) {
            if (!($hako->idToNumber[$id] > -1)) {
            } elseif ($id == $current_ID) {
                $flag = 1;
            } else {
                array_push($newAllyMember, $id);
            }
        }

        if ($flag) {
            // 脱退
            $newAlly = [];
            foreach ($island['allyId'] as $id) {
                if ($id != $ally['id']) {
                    array_push($newAlly, $id);
                }
            }
            $island['allyId'] = $newAlly;
            $ally['score'] -= $island['pop'];
            $ally['number']--;
        } else {
            // 加盟
            array_push($newAllyMember, $current_ID);
            array_push($island['allyId'], $ally['id']);
            $ally['score'] += $island['pop'];
            $ally['number']++;
        }
        $island['money'] -= $init->comCost[$init->comAlly];
        $ally['memberId'] = $newAllyMember;

        // データ格納先へ
        $hako->islands[$current_number] = $island;
        $hako->ally[$current_alliance_number] = $ally;

        // データ更新
        Util::calculates_share($hako);
        Util::allySort($hako);
        // データ書き出し
        $hako->writeAllyFile();

        // トップへ
        $html = new HtmlAlly;
        $html->allyTop($hako, $data);
    }

    /**
     * 同盟主コメント更新
     * @param       $hako ゲーム総合データ
     * @param       $data プレイヤー入力
     * @return bool       処理の成否
     */
    public function allyPactMain($hako, $data)
    {
        $ally = $hako->ally[$hako->idToAllyNumber[$data['ALLYID']]];

        if (!Util::checkPassword($ally['password'], $data['Allypact'])) {
            // Authentication failed
            // [TODO]: Viewに移す
            HakoError::wrongPassword();

            return false;
        }
        $ally['comment'] = Util::htmlEscape($data['ALLYCOMMENT']);
        $ally['title']   = Util::htmlEscape($data['ALLYTITLE']);
        $ally['message'] = Util::htmlEscape($data['ALLYMESSAGE'], 1);

        // データ書き出し
        $hako->ally[$hako->idToAllyNumber[$data['ALLYID']]] = $ally;
        $hako->writeAllyFile();

        // 変更成功
        // [TODO]: Viewに移す
        Success::allyPactOK($ally['name']);

        return true;
    }

    /**
     * 同盟関連データの再計算
     * @param  [type] &$hako [description]
     * @return bool          更新があった場合true
     */
    public function calculation(&$hako)
    {
        $calc1 = $this->delete_alliance_nobody_managed($hako);
        $calc2 = $this->update_members($hako);
        $calc3 = $this->update_alliances_score($hako);

        if ($calc1 || $calc2 || $calc3) {
            // データ書き出し
            Util::calculates_share($hako);
            AllyUtil::allySort($hako);
            $hako->writeAllyFile();

            // メッセージ出力
            // [TODO]: Viewに移す
            Success::allyDataUp();

            return true;
        }

        return false;
    }

    /**
     * 同盟の名前と記章が既存のものと重複しないか
     * @param          $game
     * @param  string  $name 同盟の名前
     * @param  integer $sign 同盟の記章
     * @return bool          しなけりゃtrue
     */
    private function check_duplicate($game, $name, $sign)
    {
        $current_number = $game->idToNumber[$current_ID];
        if ((Util::nameToNumber($game, $name) != -1)
            ||
            (
                (Util::aNameToId($game, $name) != -1)
                && (Util::aNameToId($game, $name) != $current_ID)
            )
        ) {
            HakoError::newAllyAlready();

            return;
        }

        if (Util::aMarkToId($game, $sign) != -1
            && Util::aMarkToId($game, $sign) != $current_ID
        ) {
            HakoError::markAllyAlready();

            return;
        }
    }

    /**
     * 同盟主として登録のある島が存在しないとき、当該の同盟を削除する
     * @param       &$hako ゲーム総合データ
     * @return bool        削除処理が走った場合true
     */
    private function delete_alliance_nobody_managed(&$hako)
    {
        $count = 0;

        for ($i=0; $i<$hako->allyNumber; $i++) {
            $id = $hako->ally[$i]['id'];

            if ($hako->idToNumber[$id] < 0) {
                // 配列から削除
                $hako->ally[$i]['dead'] = 1;
                $hako->idToAllyNumber[$id] = '';
                $count++;
            }
        }

        if ($count) {
            $hako->allyNumber -= $count;
            if ($hako->allyNumber < 0) {
                $hako->allyNumber = 0;
            }

            // データ格納先へ
            $hako->islands[$current_number] = $island;

            return true;
        }

        return false;
    }

    /**
     * 同盟在籍ユーザの更新
     * @param       &$hako ゲーム総合データ
     * @return bool        更新があった場合true
     */
    private function update_members(&$hako)
    {
        $flg = false;

        for ($i=0; $i<$hako->allyNumber; $i++) {
            $count = 0;
            $members = $hako->ally[$i]['memberId'];
            $new_members = [];

            foreach ($members as $id) {
                if ($hako->idToNumber[$id] > -1) {
                    array_push($new_members, $id);
                    $count++;
                }
            }

            if ($count != $hako->ally[$i]['number']) {
                $hako->ally[$i]['memberId'] = $new_members;
                $hako->ally[$i]['number'] = $count;
                $flg = true;
            }
        }

        return $flg;
    }

    /**
     * 同盟ごとの所属人口（スコア）更新
     * @param       &$hako ゲーム総合データ
     * @return bool        更新があった場合true
     */
    private function update_alliances_score(&$hako)
    {
        $flg = false;

        for ($i = 0; $i < $hako->allyNumber; $i++) {
            $score = 0;
            $members = $hako->ally[$i]['memberId'];

            foreach ($members as $id) {
                $member_island = $hako->islands[$hako->idToNumber[$id]];
                $score += $member_island['pop'];
            }

            if ($score != $hako->ally[$i]['score']) {
                $hako->ally[$i]['score'] = $score;
                $flg = true;
            }
        }

        return $flg;
    }
}
