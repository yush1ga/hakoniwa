<?php
/**
 * 箱庭諸島 S.E - 同盟管理用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once __DIR__."/config.php";
require_once MODEL."/hako-cgi.php";
require_once PRESENTER."/hako-html.php";
require_once MODEL."/Alliance.php";

use \Hakoniwa\Helper\Util_alliance as Util;

$init = new \Hakoniwa\Init;

class MakeAlly
{
    //--------------------------------------------------
    // 解散
    //--------------------------------------------------
    public function delete_alliance($hako, $data): void
    {
        global $init;

        $current_ID = $data['ISLANDID'];
        $currentAnumber = $data['ALLYNUMBER'];
        $currentNumber = $hako->idToNumber[$current_ID];
        $island = $hako->islands[$currentNumber];
        $n = $hako->idToAllyNumber[$current_ID];
        $adminMode = 0;

        // パスワードチェック
        $passCheck = isset($data['OLDPASS']) ? Util::checkPassword("", $data['OLDPASS']) : false;
        if ($passCheck) {
            $n = $currentAnumber;
            $current_ID = $hako->ally[$n]['id'];
            $adminMode = 1;
        } else {
            // passwordの判定
            if (!(Util::checkPassword($island['password'], $data['PASSWORD']))) {
                // 島 Password 間違い
                HakoError::wrongPassword();

                return;
            }
            if (!(Util::checkPassword($hako->ally[$n]['password'], $data['PASSWORD']))) {
                // 同盟 Password 間違い
                HakoError::wrongPassword();

                return;
            }
            // 念のためIDもチェック
            if ($hako->ally[$n]['id'] != $current_ID) {
                HakoError::wrongAlly();

                return;
            }
        }
        $allyMember = $hako->ally[$n]['memberId'];

        if ($adminMode && (($allyMember[0] != '') || ($n == ''))) {
            echo "削除できません。\n";

            return;
        }
        foreach ($allyMember as $id) {
            $island = $hako->islands[$hako->idToNumber[$id]];
            $newId = [];
            foreach ($island['allyId'] as $aId) {
                if ($aId != $current_ID) {
                    array_push($newId, $aId);
                }
            }
            $island['allyId'] = $newId;
        }
        $hako->ally[$n]['dead'] = 1;
        $hako->idToAllyNumber[$current_ID] = '';
        $hako->allyNumber--;

        // データ格納先へ
        $hako->islands[$currentNumber] = $island;

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
    public function joinAllyMain($hako, $data): void
    {
        global $init;

        $current_ID = $data['ISLANDID'];
        $currentAnumber = $data['ALLYNUMBER'];
        $currentNumber = $hako->idToNumber[$current_ID];
        $island = $hako->islands[$currentNumber];

        // パスワードチェック
        if (!(Util::checkPassword($island['password'], $data['PASSWORD']))) {
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
        $ally = $hako->ally[$currentAnumber];
        if ($init->allyJoinOne && ($island['allyId'][0] != '') && ($island['allyId'][0] != $ally['id'])) {
            HakoError::otherAlready();

            return;
        }

        $allyMember = $ally['memberId'];
        $newAllyMember = [];
        $flag = 0;

        foreach ($allyMember as $id) {
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
        }
        $island['money'] -= $init->comCost[$init->comAlly];
        $ally['memberId'] = $newAllyMember;

        // データ格納先へ
        $hako->islands[$currentNumber] = $island;
        $hako->ally[$currentAnumber] = $ally;

        // データ書き出し
        Util::calculates_share($hako);
        Util::allySort($hako);
        $hako->writeAllyFile();

        // トップへ
        $html = new HtmlAlly;
        $html->allyTop($hako, $data);
    }

    //--------------------------------------------------
    // 盟主コメントモード
    //--------------------------------------------------
    public function allyPactMain($hako, $data): void
    {
        $ally = $hako->ally[$hako->idToAllyNumber[$data['ALLYID']]];

        if (Util::checkPassword($ally['password'], $data['Allypact'])) {
            $ally['comment'] = Util::htmlEscape($data['ALLYCOMMENT']);
            $ally['title'] = Util::htmlEscape($data['ALLYTITLE']);
            $ally['message'] = Util::htmlEscape($data['ALLYMESSAGE'], 1);

            $hako->ally[$hako->idToAllyNumber[$data['ALLYID']]] = $ally;
            // データ書き出し
            $hako->writeAllyFile();

            // 変更成功
            Success::allyPactOK($ally['name']);
        } else {
            // password間違い
            HakoError::wrongPassword();

            return;
        }
    }
}

//------------------------------------------------------------
// Ally
//------------------------------------------------------------
class Ally extends AllyIO
{
    public $islandList;    // 島リスト
    public $targetList;    // ターゲットの島リスト
    public $defaultTarget;    // 目標補足用ターゲット

    /**
     * [readIslands description]
     * @param  [type] $cgi [description]
     * @return [type]      [description]
     */
    public function readIslands(&$cgi)
    {
        global $init;

        $m = $this->readIslandsFile();
        $this->islandList = $this->getIslandList($cgi->dataSet['defaultID'] ?? 0);

        if ($init->targetIsland == 1) {
            // 目標の島 所有の島が選択されたリスト
            $this->targetList = $this->islandList;
        } else {
            // 順位がTOPの島が選択された状態のリスト
            $this->targetList = $this->getIslandList($cgi->dataSet['defaultTarget']);
        }

        return $m;
    }

    //--------------------------------------------------
    // 島リスト生成
    //--------------------------------------------------
    public function getIslandList($select = 0)
    {
        global $init;

        $list = "";
        for ($i = 0; $i < $this->islandNumber; $i++) {
            // 同盟マークを追加
            $name = ($init->allyUse) ? Util::islandName($this->islands[$i], $this->ally, $this->idToAllyNumber) : $this->islands[$i]['name'];
            $id   = $this->islands[$i]['id'];
            // 攻撃目標をあらかじめ自分の島にする
            if (empty($this->defaultTarget)) {
                $this->defaultTarget = $id;
            }
            $s = ($id == $select) ? "selected" : "";
            // 同盟マークを追加
            $list .= ($init->allyUse) ? "<option value=\"$id\" $s>{$name}</option>\n" : "<option value=\"$id\" $s>{$name}{$init->nameSuffix}</option>\n";
        }

        return $list;
    }
}

//------------------------------------------------------------
// AllyIO
//------------------------------------------------------------
class AllyIO
{
    public $islandTurn;     // ターン数
    public $islandLastTime; // 最終更新時刻
    public $islandNumber;   // 島の総数
    public $islandNextID;   // 次に割り当てる島ID
    public $islands;        // 全島の情報を格納
    public $idToNumber;
    public $allyNumber;     // 同盟の総数
    public $ally;           // 各同盟の情報を格納
    public $idToAllyNumber; // 同盟

    //--------------------------------------------------
    // 同盟データ読みこみ
    //--------------------------------------------------
    public function readAllyFile()
    {
        global $init;

        $fileName = $init->dirName."/".$init->allyData;
        if (!is_file($fileName)) {
            return false;
        }
        $fp = fopen($fileName, "r");
        Util::lock_on_read($fp);
        $this->allyNumber = fgets($fp, READ_LINE);
        $this->allyNumber = false !== $this->allyNumber ? (int)rtrim($this->allyNumber) : 0;

        for ($i = 0; $i < $this->allyNumber; $i++) {
            $this->ally[$i] = $this->readAlly($fp);
            $this->idToAllyNumber[$this->ally[$i]["id"]] = $i;
        }
        // 加盟している同盟のIDを格納
        for ($i = 0; $i < $this->allyNumber; $i++) {
            $members = $this->ally[$i]['memberId'];
            foreach ($members as $id) {
                $n = $this->idToNumber[$id];
                if (!($n > -1)) {
                    continue;
                }
                $this->islands[$n]['allyId'][] = $this->ally[$i]['id'];
            }
        }
        Util::unlock($fp);

        return true;
    }
    //--------------------------------------------------
    // 同盟ひとつ読みこみ
    //--------------------------------------------------
    public function readAlly($fp)
    {
        $name       = rtrim(fgets($fp, READ_LINE));
        $mark       = rtrim(fgets($fp, READ_LINE));
        $color      = rtrim(fgets($fp, READ_LINE));
        $id         = rtrim(fgets($fp, READ_LINE));
        $ownerName  = rtrim(fgets($fp, READ_LINE));
        $password   = rtrim(fgets($fp, READ_LINE));
        $score      = rtrim(fgets($fp, READ_LINE));
        $number     = rtrim(fgets($fp, READ_LINE));
        $occupation = rtrim(fgets($fp, READ_LINE));
        $tmp        = rtrim(fgets($fp, READ_LINE));
        $allymember = explode(",", $tmp);
        $tmp        = rtrim(fgets($fp, READ_LINE));
        $ext        = explode(",", $tmp);
        $comment    = rtrim(fgets($fp, READ_LINE));
        $title      = rtrim(fgets($fp, READ_LINE));
        [$title, $message] = array_pad(explode("<>", $title), 2, null);

        return [
            'name'       => $name,
            'mark'       => $mark,
            'color'      => $color,
            'id'         => intval($id, 10),
            'oName'      => $ownerName,
            'password'   => $password,
            'score'      => intval($score, 10),
            'number'     => intval($number, 10),
            'occupation' => intval($occupation, 10),
            'memberId'   => $allymember,
            'ext'        => $ext,
            'comment'    => $comment,
            'title'      => $title,
            'message'    => $message,
        ];
    }
    //--------------------------------------------------
    // 同盟データ書き込み
    //--------------------------------------------------
    public function writeAllyFile()
    {
        global $init;

        $fileName = $init->dirName."/".$init->allyData;
        $fp = fopen($fileName, "w");
        ftruncate($fp, 0);
        Util::lock_on_write($fp);
        fwrite($fp, $this->allyNumber . "\n");

        for ($i = 0; $i < $this->allyNumber; $i++) {
            $this->writeAlly($fp, $this->ally[$i]);
        }
        Util::unlock($fp);

        return true;
    }

    //--------------------------------------------------
    // 同盟ひとつ書き込み
    //--------------------------------------------------
    public function writeAlly($fp, $ally): void
    {
        $members = implode(",", $ally['memberId']);
        $ext = implode(",", $ally['ext']);
        $comment = $ally['comment'] ?? '';
        $message = (isset($ally['title'], $ally['message']))
            ? $ally['title'] . '<>' . $ally['message']
            : '<>';

        fwrite(
            $fp,
            <<<EOL
{$ally['name']}
{$ally['mark']}
{$ally['color']}
{$ally['id']}
{$ally['oName']}
{$ally['password']}
{$ally['score']}
{$ally['number']}
{$ally['occupation']}
$members
$ext
$comment
$message

EOL
        );
    }

    //---------------------------------------------------
    // 全島データを読み込む
    //---------------------------------------------------
    public function readIslandsFile()
    {
        global $init;

        $fileName = "{$init->dirName}/hakojima.dat";
        if (!is_file($fileName)) {
            return false;
        }
        $fp = fopen($fileName, "r");
        Util::lock_on_read($fp);
        $this->islandTurn     = rtrim(fgets($fp, READ_LINE));
        $this->islandLastTime = rtrim(fgets($fp, READ_LINE));
        $this->islandNumber   = rtrim(fgets($fp, READ_LINE));
        $this->islandNextID   = rtrim(fgets($fp, READ_LINE));

        for ($i = 0; $i < $this->islandNumber; $i++) {
            $this->islands[$i] = $this->readIsland($fp);
            $this->idToNumber[$this->islands[$i]['id']] = $i;
            $this->islands[$i]['allyId'] = [];
        }
        Util::unlock($fp);

        if ($init->allyUse) {
            $this->readAllyFile();
        }

        return true;
    }

    //---------------------------------------------------
    // 島ひとつ読み込む
    //---------------------------------------------------
    public function readIsland($fp)
    {
        $name     = rtrim(fgets($fp, READ_LINE));

        [$name, $owner, $monster, $port, $passenger, $fishingboat, $tansaku, $senkan, $viking] = array_pad(explode(",", $name), 10, null);
        $id       = rtrim(fgets($fp, READ_LINE));
        [$id, $starturn] = explode(",", $id);
        $prize    = rtrim(fgets($fp, READ_LINE));
        $absent   = rtrim(fgets($fp, READ_LINE));
        $comment  = rtrim(fgets($fp, READ_LINE));
        [$comment, $comment_turn] = explode(",", $comment);
        $password = rtrim(fgets($fp, READ_LINE));
        $point    = rtrim(fgets($fp, READ_LINE));
        [$point, $pots] = explode(",", $point);
        $eisei    = rtrim(fgets($fp, READ_LINE));
        [$eisei0, $eisei1, $eisei2, $eisei3, $eisei4, $eisei5] = array_pad(explode(",", $eisei), 6, null);
        $zin      = rtrim(fgets($fp, READ_LINE));
        [$zin0, $zin1, $zin2, $zin3, $zin4, $zin5, $zin6] = array_pad(explode(",", $zin), 7, null);
        $item     = rtrim(fgets($fp, READ_LINE));
        [$item0, $item1, $item2, $item3, $item4, $item5, $item6, $item7, $item8, $item9, $item10, $item11, $item12, $item13, $item14, $item15, $item16, $item17, $item18, $item19] = array_pad(explode(",", $item), 20, null);
        $money    = rtrim(fgets($fp, READ_LINE));
        [$money, $lot, $gold] = array_pad(explode(",", $money), 3, null);
        $food     = rtrim(fgets($fp, READ_LINE));
        [$food, $rice] = explode(",", $food);
        $pop      = rtrim(fgets($fp, READ_LINE));
        [$pop, $peop] = explode(",", $pop);
        $area     = rtrim(fgets($fp, READ_LINE));
        $job      = rtrim(fgets($fp, READ_LINE));
        [$farm, $factory, $commerce, $mountain, $hatuden] = explode(",", $job);
        $power    = rtrim(fgets($fp, READ_LINE));
        [$taiji, $rena, $fire] = explode(",", $power);
        $tenki    = rtrim(fgets($fp, READ_LINE));
        $soccer   = rtrim(fgets($fp, READ_LINE));
        [$soccer, $team, $shiai, $kachi, $make, $hikiwake, $kougeki, $bougyo, $tokuten, $shitten] = array_pad(explode(",", $soccer), 10, null);

        return [
            'name'         => $name,
            'owner'        => $owner,
            'id'           => $id,
            'starturn'     => $starturn,
            'prize'        => $prize,
            'absent'       => $absent,
            'comment'      => $comment,
            'comment_turn' => $comment_turn,
            'password'     => $password,
            'point'        => $point,
            'pots'         => $pots,
            'money'        => $money,
            'lot'          => $lot,
            'gold'         => $gold,
            'food'         => $food,
            'rice'         => $rice,
            'pop'          => $pop,
            'peop'         => $peop,
            'area'         => $area,
            'farm'         => $farm,
            'factory'      => $factory,
            'commerce'     => $commerce,
            'mountain'     => $mountain,
            'hatuden'      => $hatuden,
            'monster'      => $monster,
            'taiji'        => $taiji,
            'rena'         => $rena,
            'fire'         => $fire,
            'tenki'        => $tenki,
            'soccer'       => $soccer,
            'team'         => $team,
            'shiai'        => $shiai,
            'kachi'        => $kachi,
            'make'         => $make,
            'hikiwake'     => $hikiwake,
            'kougeki'      => $kougeki,
            'bougyo'       => $bougyo,
            'tokuten'      => $tokuten,
            'shitten'      => $shitten,
            'land'         => $land ?? '',
            'landValue'    => $landValue ?? '',
            'command'      => $command ?? '',
            'port'         => $port ?? '',
            'ship'         => ['passenger' => $passenger, 'fishingboat' => $fishingboat, 'tansaku' => $tansaku, 'senkan' => $senkan, 'viking' => $viking],
            'eisei'        => [0 => $eisei0, 1 => $eisei1, 2 => $eisei2, 3 => $eisei3, 4 => $eisei4, 5 => $eisei5],
            'zin'          => [0 => $zin0, 1 => $zin1, 2 => $zin2, 3 => $zin3, 4 => $zin4, 5 => $zin5, 6 => $zin6],
            'item'         => [0 => $item0, 1 => $item1, 2 => $item2, 3 => $item3, 4 => $item4, 5 => $item5, 6 => $item6, 7 => $item7, 8 => $item8, 9 => $item9, 10 => $item10, 11 => $item11, 12 => $item12, 13 => $item13, 14 => $item14, 15 => $item15, 16 => $item16, 17 => $item17, 18 => $item18, 19 => $item19],
        ];
    }
}



/**
 * class Main
 */
class Main
{
    public $mode = "";
    public $dataSet = [];
    private $filter_flag = FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK;

    /**
     * 処理分岐
     * @return
     */
    public function execute()
    {
        global $init;

        $ally = new Ally;
        $cgi = new Cgi;
        $this->parseInputData();
        $cgi->getCookies();
        if (!$ally->readIslands($cgi)) {
            HTML::header();
            HakoError::noDataFile();
            HTML::footer();
            exit;
        }
        $cgi->setCookies();
        $html = new HtmlAlly;
        $com = new MakeAlly;

        switch ($this->mode) {
            // case "JoinA":
            //     $html->header();
            //     $html->newAllyTop($ally, $this->dataSet);
            //     $html->footer();

            // break;
            case "register": // 新規登録
                $html->header();
                $html->register($ally, $this->dataSet);
                $html->footer();

            break;
            case "confirm": // 登録の確認（POST）
                $model = new \Hakoniwa\Model\Alliance;
                $model->confirm($ally, $this->dataSet);

            break;
            case "establish": // 登録
                $model = new \Hakoniwa\Model\Alliance;
                $progress_error = false;
                if ($model->confirm($ally, $this->dataSet, true)) {
                    $model->establish($ally, $this->dataSet);
                } else {
                    $progress_error = true;
                }
                $html->header();
                $_ = $progress_error ? HakoError::probrem() : Success::standard();
                $html->allyTop($ally, $this->dataSet);
                $html->footer();

            break;
            // case "newally": // 同盟の結成・変更
            //     $html->header();
            //     $com->makeAllyMain($ally, $this->dataSet);
            //     $html->footer();

            // break;
            // case "delally": // 同盟の解散
            //     $html->header();
            //     $com->delete_alliance($ally, $this->dataSet);
            //     $html->footer();

            // break;
            case "delete":
                $model = new Hakoniwa\Model\Alliance;
                $progress_error = false;
                if ($model->confirm($ally, $this->dataSet, true)) {
                    $model->establish($ally, $this->dataSet);
                } else {
                    $progress_error = true;
                }

                $html->header();
                $_ = $progress_error ? HakoError::probrem() : Success::standard();
                $html->allyTop($ally, $this->dataSet);
                $html->footer();

            break;
            // 同盟の加盟・脱退
            // case "inoutally":
            //     $html->header();
            //     $com->joinAllyMain($ally, $this->dataSet);
            //     $html->footer();

            // break;
            // コメントの変更
            // case "Allypact":
            //     $html->header();
            //     $html->tempAllyPactPage($ally, $this->dataSet);
            //     $html->footer();

            // break;
            // コメントの更新
            // case "AllypactUp":
            //     $html->header();
            //     $com->allyPactMain($ally, $this->dataSet);
            //     $html->footer();

            // break;
            // case "AmiOfAlly":
            case "detail": // 同盟の情報
                $html->header();
                $html->detail($ally, $this->dataSet);
                $html->footer();

                break;

            case "prejoin": // 同盟への参加
            case "join":
                $this->dataSet["mode"] = $this->mode;
                $model = new \Hakoniwa\Model\Alliance;
                $status = $model->join($ally, $this->dataSet);

                if ($this->mode == "prejoin") {
                    header("Content-Type:application/json;charset=utf-8");
                    echo json_encode($status);

                    break;
                }

                $html->header();
                if ($status["status"] !== "true") {
                    HakoError::probrem();

                    throw new \Exception;
                }
                Success::standard();
                $model->calculation($ally);
                $html->allyTop($ally, $this->dataSet);
                $html->footer();

            break;
            default:
                $model = new \Hakoniwa\Model\Alliance;
                $html->header();
                // 箱庭データとのデータ統合処理（ターン処理に組み込んでいないため）
                if ($model->calculation($ally)) {
                    // メッセージ出力[TODO]: Viewに移す
                    Success::allyDataUp();
                } else {
                    $html->allyTop($ally, $this->dataSet);
                }
                $html->footer();

            break;
        }
    }
    //---------------------------------------------------
    // POST,GETのデータを取得
    //---------------------------------------------------
    private function parseInputData()
    {
        global $init;

        $filter_flag = $this->filter_flag;
        function get_attr($type, $key)
        {
            global $filter_flag;
            switch ($type) {
                case "post":
                    if (!filter_has_var(INPUT_POST, $key)) {
                        return;
                    }

                    return filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING, $filter_flag);
                case "get":
                    if (!filter_has_var(INPUT_GET, $key)) {
                        return;
                    }

                    return filter_input(INPUT_GET, $key, FILTER_SANITIZE_STRING, $filter_flag);
            }

            throw new \InvalidArgumentException("Input type \"{$type}\" is not defined.");
        }


        $this->mode = get_attr("post", "mode") ?? (get_attr("get", "mode") ?? "");

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->dataSet[$key] = str_replace(",", "", $value);
            }
            // if (isset($this->dataSet["Allypact"])) {
            //     $this->mode = "AllypactUp";
            // }
            // if (array_key_exists("NewAllyButton", $_POST)) {
            //     $this->mode = "newally";
            // }
            // if (array_key_exists("DeleteAllyButton", $_POST)) {
            //     $this->mode = "delally";
            // }
            // if (array_key_exists("JoinAllyButton", $_POST)) {
            //     $this->mode = "inoutally";
            // }
        }

        $this->mode = get_attr("get", "p") ?? $this->mode;

        // if (!empty($_GET["JoinA"])) {
        //     $this->mode = "JoinA";
        //     $this->dataSet["ALLYID"] = $_GET["JoinA"];
        // }
        // if (!empty($_GET["AmiOfAlly"])) {
        //     $this->mode = "AmiOfAlly";
        //     $this->dataSet["ALLYID"] = $_GET["AmiOfAlly"];
        // }
        if ($this->mode === "" && !empty($_GET["detail"])) {
            $this->mode = "detail";
            $this->dataSet["ALLYID"] = $_GET["detail"];
        }
        // if (!empty($_GET["Allypact"])) {
        //     $this->mode = "Allypact";
        //     $this->dataSet["ALLYID"] = $_GET["Allypact"];
        // }
    }
}



$start = (new Main)->execute();
