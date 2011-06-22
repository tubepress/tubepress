<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'MemoryExecutionContextTest.php';

class org_tubepress_impl_exec_ExecutionTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
	    	'org_tubepress_impl_exec_MemoryExecutionContextTest'
		));
	}
}

