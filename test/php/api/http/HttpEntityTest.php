<?php

require_once BASE . '/sys/classes/org/tubepress/api/http/HttpEntity.class.php';

class org_tubepress_api_http_HttpEntityTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_api_http_HttpEntity();
    }

    function testSetGetContentType()
    {
        $this->_sut->setContentType('hello you');
        $this->assertEquals('hello you', $this->_sut->getContentType());
    }

    /**
     * @expectedException Exception
     */
    function testSetNonStringContentType()
    {
        $this->_sut->setContentType(4);
    }

    function testSetGetChunked()
    {
        $this->_sut->setChunked(true);
        $this->assertTrue($this->_sut->isChunked());

        $this->_sut->setChunked(false);
        $this->assertFalse($this->_sut->isChunked());
    }

    /**
    * @expectedException Exception
    */
    function testSetNonStringContentEncoding()
    {
        $this->_sut->setContentEncoding(array());
    }

    function testSetContentEncoding()
    {
        $this->_sut->setContentEncoding('bla bla');
        $this->assertEquals('bla bla', $this->_sut->getContentEncoding());
    }

    /**
    * @expectedException Exception
    */
    function testSetNegativeContentLength()
    {
        $this->_sut->setContentLength(-1);
    }

    /**
     * @expectedException Exception
     */
    function testSetBadContentLength()
    {
        $this->_sut->setContentLength('something');
    }

    function testSetGetContentLength()
    {
        $this->_sut->setContentLength(55);
        $this->assertEquals(55, $this->_sut->getContentLength());

        $this->_sut->setContentLength(45.6);
        $this->assertEquals(45, $this->_sut->getContentLength());
    }

    function testSetContent()
    {
        $tests = array(

            array(1),
            array('one' => 'two'),
            'string',
            false,
            7E-10,
            null,
            new stdClass()
        );

        foreach ($tests as $test)
        {
            $this->_testSetContent($test);
        }
    }

    private function _testSetContent($value)
    {
        $this->_sut->setContent($value);
        $this->assertEquals($value, $this->_sut->getContent());
    }
}
