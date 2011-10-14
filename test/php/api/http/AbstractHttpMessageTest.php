<?php

abstract class org_tubepress_api_http_AbstractHttpMessageTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = $this->buildSut();
    }

    function testSetEntity()
    {
        $entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
        $this->_sut->setEntity($entity);
        $this->assertEquals($entity, $this->_sut->getEntity());
    }

    /**
    * @expectedException Exception
    */
    function testSetEntityNonEntity()
    {
        $this->_sut->setEntity(4);
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

    protected abstract function buildSut();

    protected function getSut()
    {
        return $this->_sut;
    }
}
