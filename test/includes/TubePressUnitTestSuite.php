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

        PHPUnit_Util_Filter::addDirectoryToFilter('/usr/share/php');
        PHPUnit_Util_Filter::addDirectoryToFilter(realpath(dirname(__FILE__) . '/..'));

    }

    public function run(PHPUnit_Framework_TestResult $result = NULL, $filter = FALSE, array $groups = array(), array $excludeGroups = array(), $processIsolation = FALSE)
    {
        if ($result === NULL) {
            $result = new PHPUnit_Framework_TestResult();
        }

        $result->addListener(new \Mockery\Adapter\Phpunit\TestListener());

        return parent::run($result, $filter, $groups, $excludeGroups, $processIsolation);
    }
}