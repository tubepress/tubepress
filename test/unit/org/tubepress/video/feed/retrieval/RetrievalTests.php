<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'HTTPRequest2Test.php';

class RetrievalTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Feed Retrieval Tests");
		$suite->addTestSuite('org_tubepress_video_feed_retrieval_HTTPRequest2Test');
		return $suite;
	}
}
?>