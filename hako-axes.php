<?php

namespace Hakoniwa\Admin;

/**
 * 箱庭諸島 S.E - アクセス解析用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once 'config.php';
require_once MODEL.'/hako-cgi.php';
require_once PRESENTER.'/hako-html.php';
require_once CONTROLLER.'/admin/axes.php';

$init  = new \Hakoniwa\Init();
$exec = new Axes();
