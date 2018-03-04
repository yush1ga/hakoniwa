<?php
namespace Hakoniwa\Admin;

/**
 * Re:箱庭諸島SE
 * @author sotalbireo <sotalbireo/hakoniwa>
 */
class Main
{
    public function __construct()
    {
        $html = new \HtmlAdmin;
        $cgi  = new \Cgi;

        $cgi->getCookies();
        $html->header();
        $html->render();
        $html->footer();
    }
}
