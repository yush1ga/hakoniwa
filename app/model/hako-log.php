<?php
/**
 * ログ関連各種関数
 */
class LogIO
{
    public $this_file = '';
    public $init;

    private $logPool       = [];
    private $secretLogPool = [];
    private $lateLogPool   = [];


    public function __construct()
    {
        global $init;
        $this->init = $init;
        $this->this_file = $init->baseDir . "/hako-main.php";
    }

    /**
     * ログファイルを後ろにずらす
     * @return void
     */
    public function slideBackLogFile(): void
    {
        for ($i = $this->init->logMax - 1; $i >= 0; $i--) {
            $j = $i + 1;
            $src = "{$this->init->dirName}/hakojima.log{$i}";
            $dist = "{$this->init->dirName}/hakojima.log{$j}";
            if (is_file($src)) {
                if (is_file($dist)) {
                    unlink($dist);
                }
                rename($src, $dist);
            }
        }
    }
    /**
     * 最近の出来事を出力
     * @param  integer $num  [description]
     * @param  integer $id   [description]
     * @param  integer $mode [description]
     * @return void          [description]
     */
    public function logFilePrint($num = 0, $id = 0, $mode = 0): void
    {
        global $init;
        $fileName = $init->dirName . "/hakojima.log" . $num;
        if (!is_file($fileName)) {
            return;
        }
        $fp = fopen($fileName, "r");
        $row = 1;

        println('<div>');
        while (false !== ($line = fgets($fp, READ_LINE))) {
            [$isSecret, $turn, $id1, $id2, $message] = explode(",", rtrim($line), 5);
            if ($isSecret == 1) {
                if (($mode == 0) || ($id1 != $id)) {
                    continue;
                }
                $message = "<strong>（機密）</strong> " . $message;
            }
            if ($id != 0 && $id != $id1 && $id != $id2) {
                continue;
            }
            if ($row == 1) {
                println('<h3 class="number">ターン', $turn, 'の出来事</h3>');
                $row++;
            }
            println('<ul class="list-unstyled"><li>', $message, '</li></ul>');
        }
        println('</div>');
        fclose($fp);
    }
    /**
     * 海域の近況を出力
     */
    public function historyPrint(): void
    {
        $fileName = $this->init->dirName."/hakojima.his";
        if (!is_file($fileName)) {
            return;
        }

        $histories = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?? [];
        foreach (array_reverse($histories) as $history) {
            [$turn, $log] = explode(",", $history, 2);
            println('<li><span class="number">ターン', $turn, '</span>：', $log, '</li>');
        }
    }
    /**
     * 発見の記録を保存
     */
    public function history(string $str): void
    {
        $fileName = $this->init->dirName."/hakojima.his";
        file_put_contents($fileName, "{$GLOBALS['ISLAND_TURN']},{$str}\n", FILE_APPEND | LOCK_EX);
    }
    /**
     * 発見の記録ログ調整
     */
    public function historyTrim(): void
    {
        $count = 0;
        $fileName = $this->init->dirName.'/hakojima.his';

        if (!is_file($fileName)) {
            return;
        }

        $histories = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?? [];
        $n_histories = count($histories);

        if (($i = $n_histories - $this->init->historyMax) > 0) {
            $fp = fopen($fileName, "w");
            for (; $i < $n_histories; $i++) {
                fwrite($fp, $histories[$i]."\n");
            }
            fclose($fp);
        }
    }
    /**
     * 通常ログ
     * @param  string $str log statement
     * @param  string $id  who done it
     * @param  string $tid who target
     * @return void
     */
    public function out($str, $id = "", $tid = ""): void
    {
        $this->logPool[] = "0,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}";
    }

    /**
     * 機密ログ
     * @param  string $str log statement
     * @param  string $id  who done it
     * @param  string $tid who target
     * @return void
     */
    public function secret($str, $id = "", $tid = ""): void
    {
        $this->secretLogPool[] = "1,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}";
    }

    /**
     * 遅延ログ
     * @param  string $str log statement
     * @param  string $id  who done it
     * @param  string $tid who target
     * @return void
     */
    public function late($str, $id = "", $tid = ""): void
    {
        $this->lateLogPool[] = "0,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}";
    }
    //---------------------------------------------------
    // ログ書き出し
    //---------------------------------------------------
    public function flush(): void
    {
        $fileName = $this->init->dirName."/hakojima.log0";

        $fp = fopen($fileName, "w");

        // 全部逆順にして書き出す
        if (!empty($this->secretLogPool)) {
            for ($i = count($this->secretLogPool) - 1; $i >= 0; $i--) {
                fwrite($fp, $this->secretLogPool[$i]."\n");
            }
        }
        if (!empty($this->lateLogPool)) {
            for ($i = count($this->lateLogPool) - 1; $i >= 0; $i--) {
                fwrite($fp, $this->lateLogPool[$i]."\n");
            }
        }
        if (!empty($this->logPool)) {
            for ($i = count($this->logPool) - 1; $i >= 0; $i--) {
                fwrite($fp, $this->logPool[$i]."\n");
            }
        }
        fclose($fp);
    }

    /**
     * お知らせを出力
     * @return void
     */
    public function infoPrint(): void
    {
        $fileName = $this->init->noticeFile;

        if ($fileName === "" || !is_file($fileName)) {
            return;
        }

        $notice_file = file_get_contents($fileName, false, null, 0, 8 * READ_LINE);

        if ($notice_file === false) {
            return;
        }

        $notice_file = preg_replace("/(?:<\/textarea>|<\?(?:php|=)|\?>)/im", "", $notice_file);
        $notice_file = preg_replace("/<script[\s\S]*?>[\s\S]*?<\/script>/im", "", $notice_file);
        $notice_file = preg_replace("/<script[\s\S]*?>[\s\S]*$/im", "", $notice_file);
        $notice_file = preg_replace("/<iframe[\s\S]*?>[\s\S]*?<\/iframe>/im", "", $notice_file);
        $notice_file = preg_replace("/<iframe[\s\S]*?>[\s\S]*$/im", "", $notice_file);

        printf(Util::htmlEscape($notice_file));
    }

    public function write_noticefile($game, $data): void
    {
        $file_name = $this->init->noticeFile;
        if ($file_name === "" || !is_file($file_name)) {
            return;
        }

        if (!Util::checkPassword("", base64_decode($data['Pwd'] ?? "", true))) {
            return;
        }

        $notice = $data["Notice"];
        $notice = preg_replace("/(?:<\/textarea>|<\?(?:php|=)|\?>)/im", "", $notice);
        $notice = preg_replace("/<script[\s\S]*?>[\s\S]*?<\/script>/im", "", $notice);
        $notice = preg_replace("/<script[\s\S]*?>[\s\S]*$/im", "", $notice);
        $notice = preg_replace("/<iframe[\s\S]*?>[\s\S]*?<\/iframe>/im", "", $notice);
        $notice = preg_replace("/<iframe[\s\S]*?>[\s\S]*$/im", "", $notice);

        try {
            file_put_contents($file_name, $notice, LOCK_EX);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage, $e->errorInfo[1], $e);
        }
    }
}

/**
 * 活動履歴テンプレート
 */
class Log extends LogIO
{
    public function discover($id, $name): void
    {
        $this->history("<a href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}</a>が発見される。");
    }
    public function changeName($name1, $name2): void
    {
        $this->history("{$this->init->tagName_}{$name1}島{$this->init->_tagName}、名称を{$this->init->tagName_}{$name2}島{$this->init->_tagName}に変更する。");
    }
    // 資金をプレゼント
    public function presentMoney($id, $name, $value): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}に、{$this->init->nameFunds}<strong>{$value}{$this->init->unitMoney}</strong>をプレゼントしました。", $id);
    }
    // 食料をプレゼント
    public function presentFood($id, $name, $value): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}に、{$this->init->nameFood}<strong>{$value}{$this->init->unitFood}</strong>をプレゼントしました。", $id);
    }
    // 受賞
    public function prize($id, $name, $pName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<strong>$pName</strong>を受賞しました。", $id);
        $this->history("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}、<strong>$pName</strong>を受賞");
    }
    // 死滅
    public function dead($name): void
    {
        $this->out("{$this->init->tagName_}${name}{$this->init->nameSuffix}{$this->init->_tagName}から人がいなくなり、<strong>滅亡</strong>しました。");
        $this->history("{$this->init->tagName_}${name}{$this->init->nameSuffix}{$this->init->_tagName}、人がいなくなり<strong>滅亡</strong>する。");
    }
    // 島の強制削除
    public function deleteIsland($name): void
    {
        $this->history("{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}に、箱庭大明神の<strong>天罰が降り</strong><span class=attention>海の中に没し</span>ました。");
    }
    public function doNothing($id, $name, $comName): void
    {
        // do not out anything
    }
    // 資金足りない
    public function noMoney($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、資金不足のため中止されました。", $id);
    }
    // 食料足りない
    public function noFood($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、備蓄食料不足のため中止されました。", $id);
    }
    // 木材足りない
    public function noWood($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、木材不足のため中止されました。", $id);
    }
    // 衛星足りない
    public function NoAny($id, $name, $comName, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、{$str}ため中止されました。", $id);
    }
    // 対象地形の種類による失敗
    public function landFail($id, $name, $comName, $kind, $point): void
    {
        $this->out('<a href="'."{$this->this_file}?Sight={$id}".'">'.$this->init->tagName_.$name.$this->init->nameSuffix.'</a>'.$this->init->_tagName.'で予定されていた'.$this->init->tagComName_.$comName.$this->init->_tagComName.'は、予定地の'.$this->init->tagName_.$point.$this->init->_tagName."が<strong>{$kind}</strong>だったため中止されました。", $id);
    }
    // 対象地形の条件による失敗
    public function JoFail($id, $name, $comName, $kind, $point): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</a>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}が条件を満たしていない<strong>{$kind}</strong>だったため中止されました。", $id);
    }
    // 都市の種類による失敗
    public function BokuFail($id, $name, $comName, $kind, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}が条件を満たした都市でなかったため中止されました。", $id);
    }
    // 周りに町がなくて失敗
    public function NoTownAround($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}の周辺に{$this->init->namePopulation}がいなかったため中止されました。", $id);
    }
    // 成功
    public function landSuc($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われました。", $id);
    }
    // 倉庫関係
    public function Souko($id, $name, $comName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}<strong>{$str}</strong>が行われました。", $id);
    }
    // 倉庫関係
    public function SoukoMax($id, $name, $comName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}の<strong>{$str}</strong>ため中止されました。", $id);
    }
    // 倉庫関係
    public function SoukoLupin($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>へ{$this->init->tagDisaster_}大怪盗が侵入したようです！！{$this->init->_tagDisaster}", $id);
    }
    // 整地系ログまとめ
    public function landSucMatome($id, $name, $comName, $point): void
    {
        $this->out("<strong>⇒</strong> {$this->init->tagName_}{$point}{$this->init->_tagName}", $id);
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われました。", $id);
    }
    // 埋蔵金
    public function maizo($id, $name, $comName, $value): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}での{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、<strong>{$value}{$this->init->unitMoney}もの埋蔵金</strong>が発見されました。", $id);
    }
    public function noLandAround($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}の周辺に陸地がなかったため中止されました。", $id);
    }
    // 卵発見
    public function EggFound($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、<strong>何かの卵</strong>を発見しました。", $id);
    }
    // 卵孵化
    public function EggBomb($id, $name, $mName, $point, $lName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の{$lName}から<strong>怪獣{$mName}</strong>が生まれました。", $id);
    }
    // お土産
    public function Miyage($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}側のお土産屋さん</strong>から<strong>{$str}</strong>もの収入がありました。", $id);
    }
    // 収穫
    public function Syukaku($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>がもたらした豊作により、さらに<strong>{$str}</strong>もの{$this->init->nameFood}が収穫されました。", $id);
    }
    // 銀行化
    public function Bank($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が銀行になりました。", $id);
    }
    // 衛星打ち上げ成功
    public function Eiseisuc($id, $name, $kind, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で{$this->init->tagComName_}{$kind}{$str}{$this->init->_tagComName}に成功しました。", $id);
    }
    // 衛星撃沈
    public function Eiseifail($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われましたが打ち上げは{$this->init->tagDisaster_}失敗{$this->init->_tagDisaster}したようです。", $id);
    }
    // 衛星破壊成功
    public function EiseiAtts($id, $tId, $name, $tName, $comName, $tEiseiname): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}</A>が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}に向けて{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行い、<strong>{$tEiseiname}</strong>に命中。<strong>$tEiseiname</strong>は跡形もなく消し飛びました。", $id, $tId);
    }
    // 衛星破壊失敗
    public function EiseiAttf($id, $tId, $name, $tName, $comName, $tEiseiname): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}の<strong>{$tEiseiname}</strong>に向けて{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行いましたが、何にも命中せず宇宙の彼方へと飛び去ってしまいました。", $id, $tId);
    }
    // 衛星レーザー
    public function EiseiLzr($id, $tId, $name, $tName, $comName, $tLname, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}</A>が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$point}{$this->init->_tagName}に向けて{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行い、<strong>{$tLname}</strong>に命中。一帯が{$str}", $id, $tId);
    }
    // 油田発見
    public function oilFound($id, $name, $point, $comName, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で<strong>{$str}</strong>の予算をつぎ込んだ{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われ、<strong>油田が掘り当てられました</strong>。", $id);
    }
    // 油田発見ならず
    public function oilFail($id, $name, $point, $comName, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で<strong>{$str}</strong>の予算をつぎ込んだ{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われましたが、油田は見つかりませんでした。", $id);
    }
    // 防衛施設、自爆セット
    public function bombSet($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>の<strong>自爆装置がセット</strong>されました。", $id);
    }
    // 防衛施設、自爆作動
    public function bombFire($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>、{$this->init->tagDisaster_}自爆装置作動！！{$this->init->_tagDisaster}", $id);
    }
    // メルトダウン発生
    public function CrushElector($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>で、{$this->init->tagDisaster_}メルトダウン発生！！{$this->init->_tagDisaster}一帯が水没しました。", $id);
    }
    // 停電発生
    public function Teiden($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で、{$this->init->tagDisaster_}停電発生！！{$this->init->_tagDisaster}", $id);
    }
    // 日照り発生
    public function Hideri($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で、{$this->init->tagDisaster_}日照りが続き{$this->init->_tagDisaster}、都市部の{$this->init->namePopulation}が減少しました。", $id);
    }
    // にわか雨発生
    public function Niwakaame($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で、{$this->init->tagDisaster_}にわか雨{$this->init->_tagDisaster}が降り、森が潤いました。", $id);
    }
    // 植林orミサイル基地
    public function PBSuc($id, $name, $comName, $point): void
    {
        $this->secret("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われました。", $id);
        $this->out("こころなしか、{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}の<strong>森</strong>が増えたようです。", $id);
    }
    // ハリボテ
    public function hariSuc($id, $name, $comName, $comName2, $point): void
    {
        $this->secret("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われました。", $id);
        $this->landSuc($id, $name, $comName2, $point);
    }
    // 記念碑、発射
    public function monFly($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が<strong>轟音とともに飛び立ちました</strong>。", $id);
    }
    // 実行許可ターン
    public function Forbidden($id, $name, $comName): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight=$id\"><span class=\"islName\">$name{$this->init->nameSuffix}</span></a>で予定されていた<span class=\"command\">$comNameは、実行が許可されませんでした。", $id);
    }
    // 管理人預かり中のため許可されない
    public function CheckKP($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、目標の島が管理人預かり中のため実行が許可されませんでした。", $id);
    }
    // 電力不足
    public function Enehusoku($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、電力不足のため中止されました。", $id);
    }
    // ミサイル撃とうとしたが天気が悪い
    public function msNoTenki($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、悪天候のため中止されました。", $id);
    }
    // ミサイル撃とうとした(or 怪獣派遣しようとした)がターゲットがいない
    public function msNoTarget($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、目標の島に人が見当たらないため中止されました。", $id);
    }
    // ミサイル撃とうとしたが基地がない
    public function msNoBase($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、<strong>ミサイル設備を保有していない</strong>ために実行できませんでした。", $id);
    }
    // ミサイル撃とうとしたが最大発射数を超えた
    public function msMaxOver($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、<strong>最大発射数を超えた</strong>ために実行できませんでした。", $id);
    }
    // ステルスミサイルログ
    public function mslogS($id, $tId, $name, $tName, $comName, $point, $missiles, $missileA, $missileB, $missileC, $missileD, $missileE): void
    {
        $missileBE = $missileB + $missileE;
        $missileH = $missiles - $missileA - $missileC - $missileBE;
        $this->secret("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$point}{$this->init->_tagName}地点に向けて{$this->init->tagComName_}{$missiles}発{$this->init->_tagComName}の{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行いました。(有効{$missileH}発/怪獣命中{$missileD}発/怪獣無効{$missileC}発/防衛{$missileBE}発/無効{$missileA}発)", $id, $tId);
        $this->late("<strong>何者か</strong>が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$point}{$this->init->_tagName}地点に向けて{$this->init->tagComName_}{$missiles}発{$this->init->_tagComName}の{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行いました。(有効{$missileH}発/怪獣命中{$missileD}発/怪獣無効{$missileC}発/防衛{$missileBE}発/無効{$missileA}発)", $tId);
    }
    // その他ミサイルログ
    public function mslog($id, $tId, $name, $tName, $comName, $point, $missiles, $missileA, $missileB, $missileC, $missileD, $missileE): void
    {
        $missileBE = $missileB + $missileE;
        $missileH = $missiles - $missileA - $missileC - $missileBE;
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$point}{$this->init->_tagName}地点に向けて{$this->init->tagComName_}{$missiles}発{$this->init->_tagComName}の{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行いました。(有効{$missileH}発/怪獣命中{$missileD}発/怪獣無効{$missileC}発/防衛{$missileBE}発/無効{$missileA}発)", $id, $tId);
    }
    // 陸地破壊弾、山に命中
    public function msLDMountain($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に命中。<strong>{$tLname}</strong>は消し飛び、荒地と化しました。", $id, $tId);
    }
    // 陸地破壊弾、海底基地に命中
    public function msLDSbase($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}に着水後爆発、同地点にあった<strong>{$tLname}</strong>は跡形もなく吹き飛びました。", $id, $tId);
    }
    // 陸地破壊弾、怪獣に命中
    public function msLDMonster($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}に着弾し爆発。陸地は<strong>怪獣{$tLname}</strong>もろとも水没しました。", $id, $tId);
    }
    // 陸地破壊弾、浅瀬に命中
    public function msLDSea1($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に着弾。海底がえぐられました。", $id, $tId);
    }
    // 陸地破壊弾、その他の地形に命中
    public function msLDLand($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に着弾。陸地は水没しました。", $id, $tId);
    }
    // 地形隆起弾、海底基地に命中
    public function msLUSbase($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}に着水後爆発、同地点にあった<strong>{$tLname}</strong>は浅瀬に埋まりました。", $id, $tId);
    }
    // 地形隆起弾、深い海に命中
    public function msLUSea0($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に着水。海底が隆起し浅瀬となりました。", $id, $tId);
    }
    // 地形隆起弾、浅瀬に命中
    public function msLUSea1($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に着弾。海底が隆起し荒地となりました。", $id, $tId);
    }
    // 地形隆起弾、怪獣に命中
    public function msLUMonster($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}に着弾。陸地は隆起し山となり、<strong>怪獣{$tLname}</strong>は生埋めとなりました。", $id, $tId);
    }
    // 地形隆起弾、その他の地形に命中
    public function msLULand($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に着弾。陸地は隆起し山となりました。", $id, $tId);
    }
    // バイオミサイル着弾、汚染
    public function msPollution($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に着弾。一帯が汚染されました。", $id, $tId);
    }
    // ステルスミサイル、怪獣に命中、硬化中にて無傷
    public function msMonNoDamageS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中、しかし硬化状態だったため効果がありませんでした。", $id, $tId);
        $this->out("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中、しかし硬化状態だったため効果がありませんでした。", $tId);
    }
    // 通常ミサイル、怪獣に命中、硬化中にて無傷
    public function msMonNoDamage($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中、しかし硬化状態だったため効果がありませんでした。", $id, $tId);
    }
    // ステルスミサイル撃ったが怪獣に叩き落とされる
    public function msMonsCaughtS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>怪獣{$tLname}</strong>に叩き落とされました。", $id, $tId);
        $this->late("-{$tPoint}の<strong>怪獣{$tLname}</strong>に叩き落とされました。", $tId);
    }
    // 通常ミサイル撃ったが怪獣に叩き落とされる
    public function msMonsCaught($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>怪獣{$tLname}</strong>に叩き落とされました。", $id, $tId);
    }
    // ステルスミサイル、怪獣に命中、殺傷
    public function msMonsKillS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>は力尽き、倒れました。", $id, $tId);
        $this->late("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>は力尽き、倒れました。", $tId);
    }
    // 通常ミサイル、怪獣に命中、殺傷
    public function msMonsKill($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>は力尽き、倒れました。", $id, $tId);
    }
    // 怪獣の死体（ステルス）
    public function msMonMoneyS($id, $tId, $tLname, $value): void
    {
        $this->secret("-<strong>怪獣{$tLname}</strong>の残骸には、<strong>{$value}{$this->init->unitMoney}</strong>の値が付きました。", $id, $tId);
        $this->late("-<strong>怪獣{$tLname}</strong>の残骸には、<strong>{$value}{$this->init->unitMoney}</strong>の値が付きました。", $tId);
    }
    // 怪獣の死体（通常）
    public function msMonMoney($id, $tId, $tLname, $value): void
    {
        $this->out("-<strong>怪獣{$tLname}</strong>の残骸には、<strong>{$value}{$this->init->unitMoney}</strong>の値が付きました。", $id, $tId);
    }
    // ステルスミサイル、怪獣に命中、ダメージ
    public function msMonsterS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>は苦しそうに咆哮しました。", $id, $tId);
        $this->late("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>は苦しそうに咆哮しました。", $tId);
    }
    // バイオミサイル、怪獣に命中、突然変異
    public function msMutation($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>に突然変異が生じました。", $id, $tId);
    }
    // 催眠弾が怪獣に命中
    public function MsSleeper($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}の<strong>怪獣{$tLname}</strong>は催眠弾によって眠ってしまったようです。", $id, $tId);
    }
    // 睡眠中の怪獣にミサイル命中
    public function MsWakeup($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で眠っていた<strong>怪獣{$tLname}</strong>にミサイルが命中、<strong>怪獣{$tLname}</strong>は目を覚ましました。", $id, $tId);
    }
    // 睡眠中の怪獣が目覚める
    public function MonsWakeup($id, $name, $lName, $point, $mName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で眠っていた<strong>怪獣{$mName}</strong>は目を覚ましました。", $id);
    }
    // 通常ミサイル、怪獣に命中、ダメージ
    public function msMonster($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>怪獣{$tLname}</strong>に命中。<strong>怪獣{$tLname}</strong>は苦しそうに咆哮しました。", $id, $tId);
    }
    // ステルスミサイル通常地形に命中
    public function msNormalS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>{$tLname}</strong>に命中、一帯が壊滅しました。", $id, $tId);
        $this->late("-{$tPoint}の<strong>{$tLname}</strong>に命中、一帯が壊滅しました。", $tId);
    }
    // 通常ミサイル通常地形に命中
    public function msNormal($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に命中、一帯が壊滅しました。", $id, $tId);
    }
    // ステルスミサイル規模減少
    public function msGensyoS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>{$tLname}</strong>に命中、規模が減少しました。", $id, $tId);
        $this->late("-{$tPoint}の<strong>{$tLname}</strong>に命中、規模が減少しました。", $tId);
    }
    // 通常ミサイル規模減少
    public function msGensyo($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に命中、規模が減少しました。", $id, $tId);
    }
    // 通常ミサイル防衛施設に命中
    public function msDefence($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->out("-{$tPoint}の<strong>{$tLname}</strong>に命中しましたが被害はありませんでした。", $id, $tId);
    }
    // ステルスミサイル防衛施設に命中
    public function msDefenceS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint): void
    {
        $this->secret("-{$tPoint}の<strong>{$tLname}</strong>に命中しましたが被害はありませんでした。", $id, $tId);
        $this->late("-{$tPoint}の<strong>{$tLname}</strong>に命中しましたが被害はありませんでした。", $tId);
    }
    // ミサイル難民到着
    public function msBoatPeople($id, $name, $achive): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}にどこからともなく<strong>{$achive}{$this->init->unitPop}もの難民</strong>が漂着しました。<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}は快く受け入れたようです。", $id);
    }
    // 怪獣派遣
    public function monsSend($id, $tId, $name, $tName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<strong>人造怪獣</strong>を建造。<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}へ送りこみました。", $id, $tId);
    }
    // 衛星消滅？！
    public function EiseiEnd($id, $name, $tEiseiname): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}の<strong>{$tEiseiname}</strong>は{$this->init->tagDisaster_}崩壊{$this->init->_tagDisaster}したようです！！", $id);
    }
    // 戦艦、怪獣に攻撃
    public function SenkanMissile($id, $tId, $name, $tName, $lName, $point, $tPoint, $tmonsName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>籍{$this->init->_tagName}<strong>{$lName}</strong>が多弾頭ミサイルを発射し、{$tPoint}の<strong>{$tmonsName}</strong>に命中しました。", $id, $tId);
    }
    // 怪獣あうち（防災都市）
    public function BariaAttack($id, $name, $point, $mName): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight={$id}\"><span class=\"islName\">{$name}{$this->init->nameSuffix}</span></a>{$point}の<strong>怪獣{$mName}</strong>が防災システムの攻撃によって力尽きました。", $id);
    }
    // 怪獣輸送に失敗
    public function MonsNoSleeper($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、睡眠中の怪獣がいなかったため中止されました。", $id);
    }
    // 怪獣輸送
    public function monsSendSleeper($id, $tId, $name, $tName, $lName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で眠っていた<strong>怪獣{$lName}</strong>が、<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}へ送りこまれました。", $id, $tId);
    }
    // 輸出
    public function sell($id, $name, $comName, $value, $unit): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<strong>{$value}{$unit}</strong>の{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行いました。", $id);
    }
    // 援助
    public function aid($id, $tId, $name, $tName, $comName, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}へ<strong>{$str}</strong>の{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行いました。", $id, $tId);
    }
    // 誘致活動
    public function propaganda($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われました。", $id);
    }
    // 放棄
    public function giveup($id, $name): void
    {
        $this->out("{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}は放棄され、<strong>滅亡</strong>しました。", $id);
        $this->history("{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}、放棄され<strong>滅亡</strong>する。");
    }
    // 油田からの収入
    public function oilMoney($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>から、<strong>{$str}</strong>の収益が上がりました。", $id);
    }
    // 油田枯渇
    public function oilEnd($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は枯渇したようです。", $id);
    }
    // 宝くじ購入
    public function buyLot($id, $name, $comName, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で<strong>{$str}</strong>分の{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が行われました。", $id);
    }
    // 宝くじ完売
    public function noLot($id, $name, $comName): void
    {
        $this->out("<strong>宝くじ完売のため</strong>、<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}は、{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が出来ませんでした。", $id);
    }
    // 宝くじ収入
    public function LotteryMoney($id, $name, $str, $syo): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<strong>宝くじ{$syo}等賞</strong>に当選！<strong>{$str}</strong>の当選金を受け取りました。", $id);
    }
    // 宝くじはずれ
    public function LotteryBlank($id, $name): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}</a>が購入していた宝くじは、全て外れてしまいました…。", $id);
    }
    // 遊園地からの収入
    public function ParkMoney($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<B>{$lName}</B>から、<B>{$str}</B>の収益が上がりました。", $id);
    }
    // 遊園地のイベント
    public function ParkEvent($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<B>{$lName}</B>でイベントが開催され、<B>{$str}</B>の{$this->init->nameFood}が消費されました。", $id);
    }
    // 遊園地のイベント増収
    public function ParkEventLuck($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<B>{$lName}</B>で開催されたイベントが成功して<B>{$str}</B>の収益が上がりました。", $id);
    }
    // 遊園地のイベント減収
    public function ParkEventLoss($id, $name, $lName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<B>{$lName}</B>で開催されたイベントが失敗して<B>{$str}</B>の損失がでました。", $id);
    }
    // 遊園地が閉園
    public function ParkEnd($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<B>{$lName}</B>は施設が老朽化したため閉園となりました。", $id);
    }
    // 怪獣、防衛施設を踏む
    public function monsMoveDefence($id, $name, $lName, $point, $mName): void
    {
        $this->out("<strong>怪獣{$mName}</strong>が<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>へ到達、<strong>{$lName}の自爆装置が作動！！</strong>", $id);
    }
    // 怪獣が自爆する
    public function MonsExplosion($id, $name, $point, $mName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$mName}</strong>が<strong>大爆発</strong>を起こしました！", $id);
    }
    // 怪獣分裂
    public function monsBunretu($id, $name, $lName, $point, $mName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>に<strong>怪獣{$mName}</strong>が分裂しました。", $id);
    }
    // 怪獣動く
    public function monsMove($id, $name, $lName, $point, $mName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が<strong>怪獣{$mName}</strong>に踏み荒らされました。", $id);
    }
    // ぞらす動く
    public function ZorasuMove($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が<strong>ぞらす</strong>に破壊されました。", $id);
    }
    // 火災
    public function fire($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が{$this->init->tagDisaster_}火災{$this->init->_tagDisaster}により壊滅しました。", $id);
    }
    // 火災未遂
    public function firenot($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が{$this->init->tagDisaster_}火災{$this->init->_tagDisaster}により被害を受けました。", $id);
    }
    // 広域被害、海の建設
    public function wideDamageSea2($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は跡形もなくなりました。", $id);
    }
    // 広域被害、怪獣水没
    public function wideDamageMonsterSea($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の陸地は<strong>怪獣{$lName}</strong>もろとも水没しました。", $id);
    }
    // 広域被害、水没
    public function wideDamageSea($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は<strong>水没</strong>しました。", $id);
    }
    // 広域被害、怪獣
    public function wideDamageMonster($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$lName}</strong>は消し飛びました。", $id);
    }
    // 広域被害、荒地
    public function wideDamageWaste($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は一瞬にして<strong>荒地</strong>と化しました。", $id);
    }
    // 地震発生
    public function earthquake($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で大規模な{$this->init->tagDisaster_}地震{$this->init->_tagDisaster}が発生！！", $id);
    }
    // 地震被害
    public function eQDamage($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は{$this->init->tagDisaster_}地震{$this->init->_tagDisaster}により壊滅しました。", $id);
    }
    // 地震被害未遂
    public function eQDamagenot($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は{$this->init->tagDisaster_}地震{$this->init->_tagDisaster}により被害を受けました。", $id);
    }
    // 飢餓
    public function starve($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}の{$this->init->tagDisaster_}{$this->init->nameFood}が不足{$this->init->_tagDisaster}しています！！", $id);
    }
    // 暴動発生
    public function pooriot($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で<strong>失業率悪化による</strong>{$this->init->tagDisaster_}暴動{$this->init->_tagDisaster}が発生！！", $id);
    }
    // 暴動被害（人口減）
    public function riotDamage1($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>で{$this->init->tagDisaster_}暴動{$this->init->_tagDisaster}により死傷者が多数出た模様です。", $id);
    }
    // 暴動被害（壊滅）
    public function riotDamage2($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が{$this->init->tagDisaster_}暴動{$this->init->_tagDisaster}により壊滅しました。", $id);
    }
    // 食料不足被害
    public function svDamage($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>に<strong>{$this->init->nameFood}を求めて住民が殺到</strong>。<strong>{$lName}</strong>は壊滅しました。", $id);
    }
    // 津波発生
    public function tsunami($id, $name): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight={$id}\" class=\"islName\">{$name}{$this->init->nameSuffix}</a>近海で{$this->init->tagDisaster_}津波{$this->init->_tagDisaster}発生！！", $id);
    }
    // 津波被害
    public function tsunamiDamage($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は{$this->init->tagDisaster_}津波{$this->init->_tagDisaster}により崩壊しました。", $id);
    }
    // 怪獣現る
    public function monsCome($id, $name, $mName, $point, $lName): void
    {
        $this->out("<a href=\"$this->this_file?Sight=$id\"><span class=\"islName\">$name{$this->init->nameSuffix}</span></a>に<strong>怪獣$mName</strong>出現！！<span class=\"islName\">$point</span>の<strong>{$lName}</strong>が踏み荒らされました。", $id);
    }
    // 船派遣した
    public function shipSend($id, $tId, $name, $sName, $point, $tName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>が{$point}{$this->init->_tagName}の<strong>{$sName}</strong>を<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}に{$this->init->tagComName_}派遣{$this->init->_tagComName}しました。", $id, $tId);
    }
    // 船帰還した
    public function shipReturn($id, $tId, $name, $sName, $point, $tName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}{$point}の<strong>{$sName}</strong>を{$this->init->tagComName_}帰還{$this->init->_tagComName}させました。", $id, $tId);
    }
    // 財宝回収
    public function RecoveryTreasure($id, $name, $sName, $value): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<strong>{$sName}</strong>が発見した<strong>{$value}億円相当</strong>の{$this->init->tagDisaster_}財宝{$this->init->_tagDisaster}を回収しました。", $id);
    }
    // 船失敗
    public function shipFail($id, $name, $comName, $kind): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、<strong>{$kind}</strong>だったため中止されました。", $id);
    }
    // ぞらす現る
    public function ZorasuCome($id, $name, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}に<strong>ぞらす</strong>出現！！", $id);
    }
    // 怪獣呼ばれる
    public function monsCall($id, $name, $mName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$mName}</strong>が天に向かって咆哮しました！", $id);
    }
    // 怪獣ワープ
    public function monsWarp($id, $tId, $name, $mName, $point, $tName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$mName}</strong>が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}にワープしました！", $id, $tId);
    }
    // 怪獣による資金増加
    public function MonsMoney($id, $name, $mName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$mName}</strong>が<strong>{$str}</strong>の金をばら撒きました。", $id);
    }
    // 怪獣による食料増加
    public function MonsFood($id, $name, $mName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$mName}</strong>が大地を踏み肥やした影響で、{$this->init->nameFood}が<strong>{$str}</strong>増産されました。", $id);
    }
    // 怪獣による資金減少
    public function MonsMoney2($id, $name, $mName, $point, $str): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}</a>{$point}の<strong>怪獣{$mName}</strong>の影響で、島の資金<strong>{$str}</strong>が喪失しました。", $id);
    }
    // 怪獣による食料減少
    public function MonsFood2($id, $name, $mName, $point, $str): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>怪獣{$mName}</strong>が大地を踏み荒らした影響で、{$this->init->nameFood}が<strong>{$str}</strong>腐敗しました。", $id);
    }
    // 地盤沈下発生
    public function falldown($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で{$this->init->tagDisaster_}地盤沈下{$this->init->_tagDisaster}が発生しました！！", $id);
    }
    // 地盤沈下被害
    public function falldownLand($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は海の中へ沈みました。", $id);
    }
    // 台風発生
    public function typhoon($id, $name): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}に{$this->init->tagDisaster_}台風{$this->init->_tagDisaster}上陸！！", $id);
    }
    // 台風被害
    public function typhoonDamage($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>は{$this->init->tagDisaster_}台風{$this->init->_tagDisaster}で飛ばされました。", $id);
    }
    // ストライキ
    public function Sto($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>社員が{$this->init->tagDisaster_}ストライキ{$this->init->_tagDisaster}を起こし<strong>商業規模</strong>が減少した模様です。", $id);
    }
    // 隕石、その他
    public function hugeMeteo($id, $name, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点に{$this->init->tagDisaster_}巨大隕石{$this->init->_tagDisaster}が落下！！", $id);
    }
    // 記念碑、落下
    public function monDamage($id, $name, $point): void
    {
        $this->out("<strong>何かとてつもないもの</strong>が<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点に落下しました！！", $id);
    }
    // 家族の力
    public function kazokuPower($id, $name, $power): void
    {
        $this->out("<strong>何かとてつもないもの</strong>が<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}に接近！<strong>{$power}発動！</strong>島の危機は免れたが、{$this->init->tagDisaster_}１人の犠牲者{$this->init->_tagDisaster}が出てしまいました…。", $id);
    }
    // 隕石、海
    public function meteoSea($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>に{$this->init->tagDisaster_}隕石{$this->init->_tagDisaster}が落下しました。", $id);
    }
    // 隕石、山
    public function meteoMountain($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>に{$this->init->tagDisaster_}隕石{$this->init->_tagDisaster}が落下、<strong>{$lName}</strong>は消し飛びました。", $id);
    }
    // 隕石、海底基地
    public function meteoSbase($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>に{$this->init->tagDisaster_}隕石{$this->init->_tagDisaster}が落下、<strong>{$lName}</strong>は崩壊しました。", $id);
    }
    // 隕石、怪獣
    public function meteoMonster($id, $name, $lName, $point): void
    {
        $this->out("<strong>怪獣{$lName}</strong>がいた<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点に{$this->init->tagDisaster_}隕石{$this->init->_tagDisaster}が落下、陸地は<strong>怪獣{$lName}</strong>もろとも水没しました。", $id);
    }
    // 隕石、浅瀬
    public function meteoSea1($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点に{$this->init->tagDisaster_}隕石{$this->init->_tagDisaster}が落下、海底がえぐられました。", $id);
    }
    // 隕石、その他
    public function meteoNormal($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点の<strong>{$lName}</strong>に{$this->init->tagDisaster_}隕石{$this->init->_tagDisaster}が落下、一帯が水没しました。", $id);
    }
    // 噴火
    public function eruption($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点で{$this->init->tagDisaster_}火山が噴火{$this->init->_tagDisaster}、<strong>山</strong>が出来ました。", $id);
    }
    // 噴火、浅瀬
    public function eruptionSea1($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点の<strong>{$lName}</strong>は、{$this->init->tagDisaster_}噴火{$this->init->_tagDisaster}の影響で陸地になりました。", $id);
    }
    // 噴火、海or海基
    public function eruptionSea($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点の<strong>{$lName}</strong>は、{$this->init->tagDisaster_}噴火{$this->init->_tagDisaster}の影響で海底が隆起、浅瀬になりました。", $id);
    }
    // 噴火、その他
    public function eruptionNormal($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}地点の<strong>{$lName}</strong>は、{$this->init->tagDisaster_}噴火{$this->init->_tagDisaster}の影響で壊滅しました。", $id);
    }
    // 海底探索の油田
    public function tansakuoil($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>が油田を発見！", $id);
    }
    // 周りに海がなくて失敗
    public function NoSeaAround($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}の周辺に海がなかったため中止されました。", $id);
    }
    // 周りに浅瀬がなくて失敗
    public function NoShoalAround($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地の{$this->init->tagName_}{$point}{$this->init->_tagName}の周辺に浅瀬がなかったため中止されました。", $id);
    }
    // 海がなくて失敗
    public function NoSea($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、予定地が海でなかったため中止されました。", $id);
    }
    // 港がないので、造船失敗
    public function NoPort($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、周辺に<strong>港</strong>がなかったため中止されました。", $id);
    }
    // 船破棄
    public function ComeBack($id, $name, $comName, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<strong>{$lName}</strong>を{$this->init->tagComName_}{$comName}{$this->init->_tagComName}しました。", $id);
    }
    // 船の最大所有数
    public function maxShip($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、<strong>船の最大所有数条約に違反してしまう</strong>ため許可されませんでした。", $id);
    }
    // 港閉鎖
    public function ClosedPort($id, $name, $lName, $point): void
    {
        $this->out("<a href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}{$this->init->_tagName}</a>{$point}の<strong>{$lName}</strong>は閉鎖したようです。", $id);
    }
    // 資金不足のため船舶放棄
    public function shipRelease($id, $tId, $name, $tName, $point, $tshipName): void
    {
        $this->late("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島所属</A>{$this->init->_tagName}<b>{$tshipName}</b>は、資金不足のため破棄されました。", $id, $tId);
    }
    // 海賊船現る
    public function VikingCome($id, $name, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}に<B>海賊船</B>出現！！", $id);
    }
    // 海賊船去る
    public function VikingAway($id, $name, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}から<B>海賊船</B>がどこかに去っていきました。", $id);
    }
    // 海賊船攻撃
    public function VikingAttack($id, $tId, $name, $tName, $lName, $point, $tPoint, $tshipName): void
    {
        $this->late("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<b>{$lName}</b>が{$tPoint}の<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}<B>{$tshipName}</B>を攻撃しました。", $id, $tId);
    }
    // 戦艦攻撃
    public function SenkanAttack($id, $tId, $name, $tName, $lName, $point, $tpoint, $tshipName): void
    {
        $this->late("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}<b>{$lName}</b>が{$tpoint}の<B>{$tshipName}</B>を攻撃しました。", $id, $tId);
    }
    // 海戦沈没
    public function BattleSinking($id, $tId, $name, $lName, $point): void
    {
        $this->late("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<b>{$lName}</b>は沈没しました。", $id, $tId);
    }
    // 船舶沈没
    public function ShipSinking($id, $tId, $name, $tName, $lName, $point): void
    {
        $this->late("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}<b>{$lName}</b>は沈没しました。", $id, $tId);
    }
    // 海賊船の財宝
    public function VikingTreasure($id, $name, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}に{$this->init->tagDisaster_}財宝が眠っている{$this->init->_tagDisaster}と噂されています。", $id);
    }
    // 財宝発見
    public function FindTreasure($id, $tId, $name, $tName, $point, $tshipName, $value): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}<B>{$tshipName}</B>が<b>{$value}億円相当</b>の{$this->init->tagDisaster_}財宝{$this->init->_tagDisaster}を発見しました。", $id);
    }
    // 海賊船、強奪
    public function RobViking($id, $name, $point, $tshipName, $money, $food): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<b>{$tshipName}</b>が<b>{$money}{$this->init->unitMoney}</b>の金と<b>{$food}{$this->init->unitFood}</b>の{$this->init->nameFood}を強奪していきました。", $id);
    }
    // 船座礁
    public function RunAground($id, $name, $lName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$point}{$this->init->_tagName}の<b>$lName</b>は{$this->init->tagDisaster_}座礁{$this->init->_tagDisaster}しました。", $id);
    }
    // 戦艦ステルスミサイル迎撃
    public function msInterceptS($id, $tId, $name, $tName, $comName, $point, $missileE): void
    {
        $this->secret("-{$this->init->tagName_}{$missileE}発{$this->init->_tagName}は<strong>戦艦</strong>によって迎撃されたようです。", $id, $tId);
        $this->late("-{$this->init->tagName_}{$missileE}発{$this->init->_tagName}は<strong>戦艦</strong>によって迎撃されたようです。", $tId);
    }
    // 戦艦通常ミサイル迎撃
    public function msIntercept($id, $tId, $name, $tName, $comName, $point, $missileE): void
    {
        $this->out("-{$this->init->tagName_}{$missileE}発{$this->init->_tagName}は<strong>戦艦</strong>によって迎撃されたようです。", $id, $tId);
    }
    // アイテム探索ログ開始
    // アイテム発見
    public function ItemFound($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}での{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、<strong>{$point}</strong>が発見されました。", $id);
    }
    // マスターソード発見
    public function SwordFound($id, $name, $mName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}の<strong>怪獣{$mName}</strong>の残骸から天空を切り裂く眩い閃光が駆け抜ける！<strong>マスターソード</strong>が発見されました。", $id);
    }
    // レッドダイヤ発見
    public function RedFound($id, $name, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}の<strong>海底探索船</strong>が<strong>{$point}</strong>を発見しました。", $id);
    }
    // ジン発見
    public function ZinFound($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}での{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、<strong>{$point}</strong>を捕まえました。", $id);
    }
    // ウィスプ発見
    public function Zin3Found($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}での{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、<strong>{$point}</strong>が襲撃してきました！<strong>マスターソード</strong>を振りかざし、見事<strong>{$point}</strong>を捕まえました。", $id);
    }
    // ルナ発見
    public function Zin5Found($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}での{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、雷鳴とともに、<strong>マナ・クリスタル</strong>が輝く。その白光の中から<strong>{$point}</strong>が現れました。", $id);
    }
    // ジン発見
    public function Zin6Found($id, $name, $comName, $point): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}での{$this->init->tagComName_}{$comName}{$this->init->_tagComName}中に、土の中から<strong>{$point}</strong>を発見！<strong>{$point}</strong>を捕まえました。", $id);
    }
    // すでにある
    public function IsFail($id, $name, $comName, $land): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、すでに<strong>{$land}</strong>があるため中止されました。", $id);
    }
    // サッカー成功
    public function SoccerSuc($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で{$this->init->tagComName_}{$comName}{$this->init->_tagComName}が実施されました。", $id);
    }
    // サッカー失敗
    public function SoccerFail($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、<strong>スタジアム</strong>が無かったため実行出来ませんでした。", $id);
    }
    // サッカー失敗2
    public function SoccerFail2($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、<strong>対戦相手</strong>が正常に選択されていなかったため実行出来ませんでした。", $id);
    }
    // 試合失敗
    public function GameFail($id, $name, $comName): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}で予定されていた{$this->init->tagComName_}{$comName}{$this->init->_tagComName}は、相手島に<strong>スタジアム</strong>が無かったため実行出来ませんでした。", $id);
    }
    // 試合勝利
    public function GameWin($id, $tId, $name, $tName, $comName, $it, $tt): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}と{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行い、<strong>{$it}点対{$tt}点</strong>で勝利しました。", $id, $tId);
    }
    // 試合敗退
    public function GameLose($id, $tId, $name, $tName, $comName, $it, $tt): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}と{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行い、<strong>{$it}点対{$tt}点</strong>で敗退しました。", $id, $tId);
    }
    // 試合引き分け
    public function GameDraw($id, $tId, $name, $tName, $comName, $it, $tt): void
    {
        $this->out("<A href=\"{$this->this_file}?Sight={$id}\">{$this->init->tagName_}{$name}{$this->init->nameSuffix}</A>{$this->init->_tagName}が<A href=\"{$this->this_file}?Sight={$tId}\">{$this->init->tagName_}{$tName}島</A>{$this->init->_tagName}と{$this->init->tagComName_}{$comName}{$this->init->_tagComName}を行い、<strong>{$it}点対{$tt}点</strong>で引き分けました。", $id, $tId);
    }
}
