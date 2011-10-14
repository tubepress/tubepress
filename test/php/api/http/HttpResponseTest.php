<?php

require_once 'AbstractHttpMessageTest.php';
require_once BASE . '/sys/classes/org/tubepress/api/http/HttpResponse.class.php';

class org_tubepress_api_http_HttpResponseTest extends org_tubepress_api_http_AbstractHttpMessageTest {

    function buildSut()
    {
        return new org_tubepress_api_http_HttpResponse();
    }


    function testSetResponseCode()
    {
        $this->getSut()->setStatusCode(134.2);
        $this->assertEquals(134, $this->getSut()->getStatusCode());

        $this->getSut()->setStatusCode(432);
        $this->assertEquals(432, $this->getSut()->getStatusCode());
    }

    function testSetStatusMessage()
    {
        $this->getSut()->setStatusMessage('hello');
        $this->assertEquals('hello', $this->getSut()->getStatusMessage());
    }

    /**
    * @expectedException Exception
    */
    function testNonStringStatusMessage()
    {
        $this->getSut()->setStatusMessage(4);
    }

    /**
    * @expectedException Exception
    */
    function testSetStatusCodeTooHigh()
    {
        $this->getSut()->setStatusCode(600);
    }

    /**
    * @expectedException Exception
    */
    function testSetStatusCodeTooLow()
    {
        $this->getSut()->setStatusCode(99);
    }

    /**
     * @expectedException Exception
     */
    function testSetResponseCodeNonNumeric()
    {
        $this->getSut()->setStatusCode('something');
    }
}
