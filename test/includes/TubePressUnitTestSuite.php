<?php

require_once 'testBootStrap.php';

class TubePressUnitTestSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct(array $tests)
    {
        parent::__construct();

        foreach ($tests as $test) {
            $this->addTestSuite($test);
        }
    }
}
