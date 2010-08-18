<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'VideoTest.php';
require_once 'factory/FactoryTests.php';
require_once 'feed/inspection/InspectionTests.php';
require_once 'feed/provider/ProviderTests.php';
require_once 'feed/retrieval/RetrievalTests.php';

class VideoTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Utility Tests");
		$suite->addTestSuite('org_tubepress_video_VideoTest');
                $suite->addTestSuite('InspectionTests');
		$suite->addTestSuite('ProviderTests');
		$suite->addTestSuite('RetrievalTests');
		$suite->addTestSuite(FactoryTests::suite());
		return $suite;
	}
}
?>
