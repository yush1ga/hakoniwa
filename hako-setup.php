<?php
namespace Hakoniwa;

require_once "config.php";

/**
 * Setup
 */
class Setup
{
    use \Hakoniwa\Model\FileIO;

    public function __construct($argument)
    {
        $this->init = new \Hakoniwa\Init;
    }

    private function check_version(): void
    {
    }
}
