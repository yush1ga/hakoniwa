<?php
namespace Hakoniwa\Admin;

/**
 * Re:箱庭諸島SE バックアップモジュール
 */

require_once 'config.php';
require_once MODELPATH.'/admin.php';
require_once MODELPATH.'/hako-cgi.php';
require_once PRESENTER.'/hako-html.php';
require_once CONTROLLERPATH.'/admin/mente.php';

$init  = new \Init();
$start = new \Mente();
$start->execute();
