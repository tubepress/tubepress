<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/gdata/inspection/SimpleFeedInspectionService.class.php';

class org_tubepress_gdata_inspection_SimpleFeedInspectionServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_gdata_inspection_SimpleFeedInspectionService();
	}
	
	function testGetTotalResultCount()
	{
		$dom = DOMDocument::loadXML($this->getSampleXmlTotal());
		$this->assertEquals(100, $this->_sut->getTotalResultCount($dom));
	}
	
	/**
     * @expectedException Exception
     */
	function testGetTotalResultNonNumeric()
	{
		$badxml = str_replace("100", "fake", $this->getSampleXmlTotal());
		$dom = DOMDocument::loadXML($badxml);
		$this->_sut->getTotalResultCount($dom);
	}
	
	/**
     * @expectedException Exception
     */
	function testGetTotalResultMissing()
	{
		$badxml = str_replace("<openSearch:totalResults>100</openSearch:totalResults>", "", $this->getSampleXmlTotal());
		$dom = DOMDocument::loadXML($badxml);
		$this->_sut->getTotalResultCount($dom);
	}
	
	function testGetQueryResult()
	{
		$dom = DOMDocument::load(dirname(__FILE__) . "/../../../../sample_feed.xml");
		$this->assertEquals(25, $this->_sut->getQueryResultCount($dom));
	}
	
	function testGetQueryResultNoEntries()
	{
		$dom = DOMDocument::loadXML($this->getSampleXmlTotal());
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