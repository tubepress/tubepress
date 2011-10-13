<?php

require_once BASE . '/sys/classes/org/tubepress/api/http/HttpRequest.class.php';

class org_tubepress_api_http_HttpRequestTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_api_http_HttpRequest(org_tubepress_api_http_HttpRequest::HTTP_METHOD_GET, 'http://tubepress.org/foo.html');
    }

    /**
     * @expectedException Exception
     */
    function testGetHeaderBadName()
    {
        $this->_sut->getHeaderValue(6);
    }

    /**
     * @expectedException Exception
     */
    function testSetHeaderBadValue()
    {
        $this->_sut->setHeader(5, 'two');
    }

    /**
     * @expectedException Exception
     */
    function testSetHeaderBadName()
    {
        $this->_sut->setHeader(5, 'two');
    }

    function testSetGetHeader()
    {

        $this->_sut->setHeader('something', 'else');
        $this->assertEquals('else', $this->_sut->getHeaderValue('something'));

        $this->assertEquals(array('something' => 'else'), $this->_sut->getAllHeaders());

        $this->_sut->setHeader('foo', 'bar');
        $this->_sut->removeHeaders('something');
        $this->assertEquals(array('foo' => 'bar'), $this->_sut->getAllHeaders());
    }


    function testGetHeaderNotExist()
    {

        $this->assertFalse($this->_sut->containsHeader('something'));
        $this->assertNull($this->_sut->getHeaderValue('something'));
    }

    function testSetUrlUrl()
    {
        $url = new org_tubepress_api_url_Url('http://tubepress.org/foo.html');
        $this->_sut->setUrl($url);
        $url = $this->_sut->getUrl();

        $this->assertTrue($url instanceof org_tubepress_api_url_Url);
        $this->assertEquals('http://tubepress.org/foo.html', $url->toString());
    }

    /**
    * @expectedException Exception
    */
    function testSetUrlBadArg()
    {
        $this->_sut->setUrl(4);
    }

    function testSetUrlString()
    {
        $this->_sut->setUrl('http://tubepress.org/foo.html');
        $url = $this->_sut->getUrl();

        $this->assertTrue($url instanceof org_tubepress_api_url_Url);
        $this->assertEquals('http://tubepress.org/foo.html', $url->toString());
    }

    function testSetGetMethod()
    {
        $this->_sut->setMethod('pOsT');
        $this->assertEquals('POST', $this->_sut->getMethod());
    }

    /**
     * @expectedException Exception
     */
    function testSetBadMethod()
    {
        $this->_sut->setMethod('something dumb');
    }
}
