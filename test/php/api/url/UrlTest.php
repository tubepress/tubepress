<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/api/url/Url.class.php';

class org_tubepress_api_url_UrlTest extends TubePressUnitTest {

	private $_sut;
	private $_url;

	function setup()
	{
	    $this->_url = 'http://user@tubepress.org:994/something/index.php?one=two&three=four#fragment';
	}

    function testSet()
    {
        $this->_sut = new org_tubepress_api_url_Url($this->_url);
        $this->_sut->setQueryVariable('three', 'five');
        $this->_sut->setQueryVariable('nine', 'ten');

        $this->assertEquals('http://user@tubepress.org:994/something/index.php?one=two&three=five&nine=ten#fragment', $this->_sut->toString());
    }

    function testUnset()
    {
        $this->_sut = new org_tubepress_api_url_Url($this->_url);
        $this->_sut->unsetQueryVariable('three');

        $this->assertEquals('http://user@tubepress.org:994/something/index.php?one=two#fragment', $this->_sut->toString());
    }

	function testConstructor()
	{
	    $this->_sut = new org_tubepress_api_url_Url($this->_url);

	    $this->assertEquals($this->_url, $this->_sut->toString());
	}
}