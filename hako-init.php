<?php

declare(strict_types=1);

namespace Hakoniwa;

/**
 * Re:箱庭諸島 S.E.
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E. ver23_r09 by SERA
 * @author sota_n <@sota_n>
 */

require_once "config.php";
require_once "hako-init-default.php";
use \Hakoniwa\InitDefault;

class Init extends InitDefault
{
    // サイトのURL
    public $baseDir = "http://localhost:8000";

    // ゲームタイトル
    public $title      = "Re:箱庭諸島";

    // 管理人の名前と連絡先
    public $admin_name  = "管理人";
    public $admin_address  = 'https://twitter.com/twitter';
}
