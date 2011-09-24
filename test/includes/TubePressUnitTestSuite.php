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


	if (is_callable('PHP_CodeCoverage_Filter::getInstance')) {

		$filter = PHP_CodeCoverage_Filter::getInstance();

        	$filter->addDirectoryToBlacklist('/usr/share/php');
        	$filter->addDirectoryToBlacklist(realpath(dirname(__FILE__) . '/..'));
	
	} else {

		PHPUnit_Util_Filter::addDirectoryToFilter('/usr/share/php');
		PHPUnit_Util_Filter::addDirectoryToFilter(realpath(dirname(__FILE__) . '/..'));
	}
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
