<?php

require_once 'AbstractHttpMessageTest.php';
require_once BASE . '/sys/classes/org/tubepress/api/http/HttpRequest.class.php';

class org_tubepress_api_http_HttpRequestTest extends org_tubepress_api_http_AbstractHttpMessageTest {

    function buildSut()
    {
        return new org_tubepress_api_http_HttpRequest(org_tubepress_api_http_HttpRequest::HTTP_METHOD_GET, 'http://tubepress.org/foo.html');
    }

    function testToString()
    {
        $expected = 'GET to <a href="http://tubepress.org/foo.html">URL</a>';
        $this->assertEquals($expected, $this->getSut()->toString());
        $this->assertEquals($expected, $this->getSut()->__toString());
    }

    function testSetUrlUrl()
    {
        $url = new org_tubepress_api_url_Url('http://tubepress.org/foo.html');
        $this->getSut()->setUrl($url);
        $url = $this->getSut()->getUrl();

        $this->assertTrue($url instanceof org_tubepress_api_url_Url);
        $this->assertEquals('http://tubepress.org/foo.html', $url->toString());
    }

    /**
    * @expectedException Exception
    */
    function testSetUrlBadArg()
    {
        $this->getSut()->setUrl(4);
    }

    function testSetUrlString()
    {
        $this->getSut()->setUrl('http://tubepress.org/foo.html');
        $url = $this->getSut()->getUrl();

        $this->assertTrue($url instanceof org_tubepress_api_url_Url);
        $this->assertEquals('http://tubepress.org/foo.html', $url->toString());
    }

    function testSetGetMethod()
    {
        $this->getSut()->setMethod('pOsT');
        $this->assertEquals('POST', $this->getSut()->getMethod());
    }

    /**
     * @expectedException Exception
     */
    function testSetBadMethod()
    {
        $this->getSut()->setMethod('something dumb');
    }
}
