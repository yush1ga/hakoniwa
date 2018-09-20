<?php

declare(strict_types=1);

namespace Hakoniwa\Model;

class Cgi
{
    public $dataset;

    private $filter_flag = FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK;

    public function parse_input_data()
    {
        if (!empty($_GET)) {
        }
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->dataset[$key] = str_replace(",", "", $value);
            }
        }
    }

    private function dataset_optimization()
    {
        if ($this->dataset) {
        }
    }
}
