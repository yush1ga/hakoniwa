<?php
/**
 * 箱庭諸島 S.E - メインファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once __DIR__."/config.php";
require_once MODEL."/hako-cgi.php";
require_once MODEL."/hako-file.php";
require_once MODEL."/hako-turn.php";
require_once PRESENTER."/hako-html.php";
require_once CONTROLLER."/main.php";

$init  = new \Hakoniwa\Init;
$start = new Main;
$start->execute();
