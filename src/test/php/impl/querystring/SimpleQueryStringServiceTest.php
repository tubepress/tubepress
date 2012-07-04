<?php

require_once BASE . '/sys/classes/org/tubepress/impl/querystring/SimpleQueryStringService.class.php';

class org_tubepress_impl_querystring_SimpleQueryStringServiceTest extends TubePressUnitTest {

    private $_sut;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_querystring_SimpleQueryStringService();
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
