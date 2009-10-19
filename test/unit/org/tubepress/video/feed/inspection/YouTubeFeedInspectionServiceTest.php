<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/video/feed/inspection/YouTubeFeedInspectionService.class.php';

class org_tubepress_video_feed_inspection_YouTubeFeedInspectionServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_video_feed_inspection_YouTubeFeedInspectionService();
	}
	
	function testGetTotalResultCount()
	{
		$dom = $this->getSampleXmlTotal();
		$this->assertEquals(100, $this->_sut->getTotalResultCount($dom));
	}
	
	/**
     * @expectedException Exception
     */
	function testGetTotalResultNonNumeric()
	{
		$badxml = str_replace("100", "fake", $this->getSampleXmlTotal());
		$this->_sut->getTotalResultCount($badxml);
	}
	
	/**
     * @expectedException Exception
     */
	function testGetTotalResultMissing()
	{
		$badxml = str_replace("<openSearch:totalResults>100</openSearch:totalResults>", "", $this->getSampleXmlTotal());
		$this->_sut->getTotalResultCount($badxml);
	}
	
	function testGetQueryResult()
	{
		$dom = file_get_contents(dirname(__FILE__) . "/../../../../../sample_feed.xml");
		$this->assertEquals(21, $this->_sut->getQueryResultCount($dom));
	}
	
	function testGetQueryResultNoEntries()
	{
		$dom = $this->getSampleXmlTotal();
		$this->assertEquals(0, $this->_sut->getQueryResultCount($dom));
	}
	
	function getSampleXmlTotal()
	{
		return <<<EOT
<xml version='1.0' encoding='UTF-8'>
	<feed xmlns='http://www.w3.org/2005/Atom'
		xmlns:openSearch='http://a9.com/-/spec/opensearch/1.1/'>
		<openSearch:totalResults>100</openSearch:totalResults>
	</feed>
</xml>
EOT;
	}
}
?>