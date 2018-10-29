<?php

namespace Hakoniwa\Admin;

require_once MODEL.'/admin.php';

/**
 * 箱庭諸島 S.E
 * @author hiro <@hiro0218>
 */

class BF extends \Admin
{
    public function execute(): void
    {
        $html = new \HtmlBF();
        $hako = new \HakoBF();
        $cgi = new \Cgi();
        $this->parseInputData();
        $hako->init($this);
        $cgi->getCookies();
        $html->header();

        if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
            switch ($this->mode) {
                case "TOBF":
                    $this->toMode($this->dataSet['ISLANDID'], $hako);
                    $hako->init($this);

                    break;

                case "FROMBF":
                    $this->fromMode($this->dataSet['ISLANDID'], $hako);
                    $hako->init($this);

                    break;
            }
        }
        $html->main($this->dataSet, $hako);
        $html->footer();
    }

    public function toMode($id, &$hako): void
    {
        global $init;

        if ($id) {
            $num = $hako->idToNumber[$id];
            if (!$hako->islands[$num]['isBF']) {
                $hako->islands[$num]['isBF'] = 1;
                $hako->islandNumberBF++;
                require_once APP.'/model/hako-turn.php';
                \Turn::islandSort($hako);
                $hako->writeIslandsFile();
            }
        }
    }

    public function fromMode($id, &$hako): void
    {
        global $init;

        if ($id) {
            $num = $hako->idToNumber[$id];
            if ($hako->islands[$num]['isBF']) {
                $hako->islands[$num]['isBF'] = 0;
                $hako->islandNumberBF--;
                require_once APP.'/model/hako-turn.php';
                \Turn::islandSort($hako);
                $hako->writeIslandsFile();
            }
        }
    }
}
