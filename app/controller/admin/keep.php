<?php

namespace Hakoniwa\Admin;

require_once MODEL.'/admin.php';

/**
 * 箱庭諸島 S.E
 * @author hiro <@hiro0218>
 */
class Keep extends \Admin
{
    public function execute(): void
    {
        $html = new \HTMLKeep();
        $cgi = new \Cgi();
        $hako = new \HakoKP();
        $this->parseInputData();
        $hako->init($this);
        $cgi->getCookies();
        $html->header();

        switch ($this->mode) {
            case "TOKP":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->toMode($this->dataSet['ISLANDID'], $hako);
                    $hako->init($this);
                }

                break;

            case "FROMKP":
                if (\Util::checkPassword('', $this->dataSet['PASSWORD'])) {
                    $this->fromMode($this->dataSet['ISLANDID'], $hako);
                    $hako->init($this);
                }

                break;
        }
        $html->main($this->dataSet, $hako);
        $html->footer();
    }

    public function toMode($id, &$hako): void
    {
        global $init;

        if ($id) {
            $num = $hako->idToNumber[$id];
            if (!$hako->islands[$num]['keep']) {
                $hako->islands[$num]['keep'] = 1;
                $hako->islandNumberKP++;
                //require 'hako-turn.php';
                //Turn::islandSort($hako);
                $hako->writeIslandsFile();
            }
        }
    }

    public function fromMode($id, &$hako): void
    {
        global $init;

        if ($id) {
            $num = $hako->idToNumber[$id];
            if ($hako->islands[$num]['keep']) {
                $hako->islands[$num]['keep'] = 0;
                $hako->islandNumberKP--;
                //require 'hako-turn.php';
                //Turn::islandSort($hako);
                $hako->writeIslandsFile();
            }
        }
    }
}
