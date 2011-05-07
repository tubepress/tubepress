<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

abstract class org_tubepress_impl_embedded_AbstractEmbeddedStrategyTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->initFakeIoc();
        $_SERVER['HTTP_USER_AGENT'] = 'foo';
        $this->_sut = $this->buildSut();
    }
    
    function testExec()
    {
        $this->_sut->start();
        $result = $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, 'videoid');
        $this->assertEquals($this->expected(), $result);
    }
    
    /**
     * @expectedException Exception
     */
    function testExecWrongArgCount()
    {
        $this->_sut->start();
        $result = $this->_sut->execute('bla');
        
    }
    
    function testStart()
    {
        $this->_sut->start();
    }
    
    function testStop()
    {
        $this->_sut->stop();
    }
    
    /**
     * @expectedException Exception
     */
    function testCanHandleWrongArgCount()
    {
        $this->_sut->canHandle('somearg');
    }
    
    abstract function expected();
    
    abstract function buildSut();
    
    protected function getSut()
    {
        return $this->_sut;
    }
}
