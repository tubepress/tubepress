<?php

abstract class org_tubepress_impl_embedded_commands_AbstractCommandTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        parent::setUp();
        $_SERVER['HTTP_USER_AGENT'] = 'foo';
        $this->_sut = $this->buildSut();
    }

    
    abstract function expected();
    
    abstract function buildSut();
    
    protected function getSut()
    {
        return $this->_sut;
    }
}
