<?php
/**
 * Re:箱庭諸島 S.E
 * @author Sotalbireo
 */
class Admin
{
    public $mode;
    public $dataSet = [];
    private final $pointer = [
        'filter' => FILTER_VALIDATE_INT,
        'options' => [
            'min_range' => 0,
            'max_range' => $init->islandSize
        ]
    ];
    private final $args = [
        'mode',
        'DEVELOPEMODE',
        'ISLANDID'
        'PASSWORD',

        'defaultID' => FILTER_VALIDATE_INT,
        'defaultTarget' => FILTER_VALIDATE_INT,
        'defaultX',
        'defaultY',
        'defaultLAND',
        'defaultMONSTER',
        'defaultSHIP',
        'defaultLEVEL',
        'defaultImg',

        'POINTX',
        'POINTY',
        'LAND',
        'MONSTER',
        'SHIP',
        'LEVEL',
        'IMG'
    ];

    public function parseInputData()
    {
        $this->mode = filter_input(INPUT_POST, 'mode') ?? "";

        if (!empty($_POST)) {
            while (list($name, $value) = each($_POST)) {
                $this->dataSet[$name] = str_replace(",", "", $value);
            }
        }
        // $this->dataSet = filter_input_array(INPUT_POST, $this->args);
    }
}
