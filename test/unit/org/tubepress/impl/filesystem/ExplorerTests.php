<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'FsExplorerTest.php';

class ExplorerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Filesystem Tests');
		$suite->addTestSuite('org_tubepress_impl_filesystem_FsExplorerTest');
		return $suite;
	}
}
?>
