<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'FsExplorerTest.php';

class org_tubepress_impl_filesystem_FilesystemTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_filesystem_FsExplorerTest'
		));
	}
}
