<?php
/**
 * Re:箱庭諸島 S.E
 * @author Sotalbireo
 */
class Admin
{
    protected $mode;
    protected $dataSet = [];
    protected $d_dataSet = [];
    private const POINTER = [
        'filter' => FILTER_VALIDATE_INT,
        'options' => [
            'min_range' => 0,
            'max_range' => 10
        ]
    ];
    private const ARGS = [
        'mode',
        'DEVELOPEMODE',
        'ISLANDID',
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

    public function parseInputData(): void
    {
        $this->mode = filter_input(INPUT_POST, 'mode') ?? "";

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->dataSet[$key] = str_replace(",", "", $value);
            }
        }
    }
    public function d_parseInputData(): void
    {
        $this->d_dataSet = filter_input_array(INPUT_POST, array_merge($this::ARGS, $this->vargs));
    }
}
