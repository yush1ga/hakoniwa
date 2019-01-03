<?php

namespace Hakoniwa;

/**
 * 箱庭諸島 S.E - プレゼント定義用ファイル -
 * @copyright 箱庭諸島 ver2.30
 * @since 箱庭諸島 S.E ver23_r09 by SERA
 * @author hiro <@hiro0218>
 */

require_once 'config.php';

$init  = new \Hakoniwa\Init;
$start = new Admin\Present;
$start->execute();
