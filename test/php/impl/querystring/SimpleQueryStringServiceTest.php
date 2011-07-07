<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/querystring/SimpleQueryStringService.class.php';

class org_tubepress_impl_querystring_SimpleQueryStringServiceTest extends TubePressUnitTest {

    private $_sut;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_querystring_SimpleQueryStringService();
    }

    public function testGetPageNumNothingSet()
    {
        $result = $this->_sut->getPageNum(array());
        $this->assertEquals(1, $result);
    }

    public function testGetPageNumLessThanOne()
    {
        $this->assertEquals(1, $this->_sut->getPageNum(array("tubepress_page" => -1)));
    }

    public function testSearchTerms()
    {
        $this->assertEquals('kkjjhh', $this->_sut->getSearchTerms(array("tubepress_search" => "kkjjhh")));
    }

    public function testGetCustomVideo()
    {
        $this->assertEquals('word', $this->_sut->getCustomVideo(array("tubepress_video" => "word")));
    }

    public function testGetShortcode()
    {
        $this->assertEquals('fake', $this->_sut->getShortcode(array("tubepress_shortcode" => "fake")));
    }

    public function testGetShortcodeNoShortcode()
    {
        $this->assertEquals('', $this->_sut->getShortcode(array()));
    }

    public function testGetPageNumNonNumeric()
    {
        $this->assertEquals(1, $this->_sut->getPageNum(array("tubepress_page" => "fake")));
    }

    public function testGetFullUrlHttpsOn()
    {
        $serverVars = array("HTTPS" => "on",
                            "SERVER_PORT" => "443",
                            "SERVER_NAME" => "fake.com",
                            "REQUEST_URI" => "/index.html");
        $this->assertEquals("https://fake.com:443/index.html",
            $this->_sut->getFullUrl($serverVars));
    }

    public function testGetFullUrlHttpsOff()
    {
        $serverVars = array("HTTPS" => "off",
                            "SERVER_PORT" => "80",
                            "SERVER_NAME" => "fake.com",
                            "REQUEST_URI" => "/index.html");
        $this->assertEquals("http://fake.com/index.html",
            $this->_sut->getFullUrl($serverVars));
    }
}
