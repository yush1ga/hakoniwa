<?php
namespace Hakoniwa\Admin;
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
        $html = new \HtmlAxes();
        $cgi  = new \Cgi();
        $this->parseInputData();
        $cgi->getCookies();
        $html->header();

        switch ($this->mode) {
            case "auth":
                if ($this->passCheck()) {
                    $html->main($this->dataSet);
                }
                break;
            default:
                $html->passwdChk();
                break;
        }
        $html->footer();
    }
}
