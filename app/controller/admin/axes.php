<?php

namespace Hakoniwa\Admin;

require_once MODEL.'/admin.php';
/**
 * 箱庭諸島 S.E
 * @author hiro <@hiro0218>
 */
class Axes extends \Admin
{
    public $init;

    public function __construct()
    {
        global $init;
        $this->init = $init;
        $html = new \HtmlAxes;
        $cgi  = new \Cgi;
        $this->parseInputData();
        $cgi->getCookies();
        $html->header();

        if (isset($this->dataSet['PASSWORD'])) {
            if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                $html->main($this->dataSet);
            } else {
                \HakoError::wrongPassword();
            }
        } else {
            $html->passwdChk();
        }

        $html->footer();
    }
}
