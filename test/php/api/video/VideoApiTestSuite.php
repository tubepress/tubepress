<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'VideoTest.php';

class org_tubepress_api_video_VideoApiTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_api_video_VideoTest'
		));
	}
}
