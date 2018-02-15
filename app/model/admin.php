<?php
/**
 * 箱庭諸島 S.E
 * @author Sotalbireo
 */
class Admin {
    public $mode;
    public $dataSet = [];

    public function parseInputData() {
        $this->mode = $_POST['mode'] ?? "";

        if (!empty($_POST)) {
            while (list($name, $value) = each($_POST)) {
                $this->dataSet[$name] = str_replace(",", "", $value);
            }
        }
    }

    public function passCheck():bool {
        global $init;

        if (file_exists($init->passwordFile)) {
            $fp = fopen($init->passwordFile, "r");
            $masterPassword = chop(fgets($fp, READ_LINE));
            fclose($fp);
        }
        if (!isset($this->dataSet['PASSWORD'])) {
            HakoError::wrongPassword();

            return false;
        }
        if (password_verify($this->dataSet['PASSWORD'], $masterPassword)) {
            return true;
        } else {
            HakoError::wrongPassword();

            return false;
        }
    }
}
