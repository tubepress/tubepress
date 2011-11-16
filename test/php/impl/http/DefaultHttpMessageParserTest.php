<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/DefaultHttpMessageParser.class.php';

class org_tubepress_impl_http_DefaultHttpMessageParserTest extends TubePressUnitTest {

    private $_sut;


    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_http_DefaultHttpMessageParser();
    }

    function testGetHeaderArrayNullMessage()
    {
        $this->assertEquals(array(), $this->_sut->getArrayOfHeadersFromRawHeaderString(null));
    }

    function testGetHeaderArrayBadMessage()
    {
        $this->assertEquals(array(), $this->_sut->getArrayOfHeadersFromRawHeaderString('this is a string with nothing in it'));
    }

    function testGetHeaderArraySomeBadMessages()
    {
        $expected = array(
            'Header' => 'Value',
            'Another' => 'header'
        );
        $this->assertEquals($expected, $this->_sut->getArrayOfHeadersFromRawHeaderString("Header: Value\r\ntthis is a string with nothing in it\r\nAnother: \theader\r\nHeader21:\r\n"));
    }

    function testGetHeaderArrayMultipleHeaders()
    {
        $expected = array(
                'Header' => array('Value', 'something else'),
                'Another' => 'header'
        );
        $this->assertEquals($expected, $this->_sut->getArrayOfHeadersFromRawHeaderString("Header: Value\r\nAnother: \theader\r\nHeader: something else\r\n"));
    }

    function testGetHeaderAsStringBadHeader()
    {
        $message = \Mockery::mock(org_tubepress_api_http_HttpMessage::_);
        $message->shouldReceive('getAllHeaders')->once()->andReturn('foobar');
        $result = $this->_sut->getHeaderArrayAsString($message);
        $this->assertEquals('', $result);
    }

    function testGetHeaderAsString()
    {
        $message = \Mockery::mock(org_tubepress_api_http_HttpMessage::_);
        $message->shouldReceive('getAllHeaders')->once()->andReturn(array('one' => 'two', 'three' => 'four'));
        $result = $this->_sut->getHeaderArrayAsString($message);
        $this->assertEquals("one: two\r\nthree: four\r\n", $result);
    }

    function testGetHeadersStringFromRawHttpMessage()
    {
        $result = $this->_sut->getHeadersStringFromRawHttpMessage("headers\r\n\r\nHeaders");
        $this->assertEquals('headers', $result);
    }

    function testGetHeadersStringFromRawHttpMessageBadMessage()
    {
        $result = $this->_sut->getHeadersStringFromRawHttpMessage("something");
        $this->assertEquals('something', $result);
    }

    function testGetHeadersStringFromRawHttpMessageNullMessage()
    {
        $result = $this->_sut->getHeadersStringFromRawHttpMessage(null);
        $this->assertNull($result);
    }

    function testGetBodyStringFromRawHttpMessage()
    {
        $result = $this->_sut->getBodyStringFromRawHttpMessage("headers\r\n\r\nbody");
        $this->assertEquals('body', $result);
    }

    function testGetBodyStringFromRawHttpMessageBadMessage()
    {
        $result = $this->_sut->getBodyStringFromRawHttpMessage("something");
        $this->assertNull($result);
    }

    function testGetBodyStringFromRawHttpMessageNullMessage()
    {
        $result = $this->_sut->getBodyStringFromRawHttpMessage(null);
        $this->assertNull($result);
    }
}

