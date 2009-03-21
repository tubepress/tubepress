<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'org/tubepress/cache/CacheTests.php';
require_once 'org/tubepress/embedded/EmbeddedTests.php';
//require_once 'org/tubepress/gallery/GalleryTests.php';
//require_once 'org/tubepress/gdata/feed/retrieval/RetrievalTests.php';
require_once 'org/tubepress/gdata/inspection/InspectionTests.php';

class TubePressUnitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");
		$suite->addTest(CacheTests::suite());
		$suite->addTest(EmbeddedTests::suite());
		//$suite->addTest(GalleryTests::suite());
		$suite->addTest(InspectionTests::suite());
		return $suite;
	}
}

?>
