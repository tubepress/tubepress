<?php

class org_tubepress_impl_environment_SimpleEnvironmentDetectorTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_environment_SimpleEnvironmentDetector();
    }
    
    function testIsPro()
    {
        $this->assertFalse($this->_sut->isPro());
    }
    
    function testIsWordPress()
    {
        $this->assertFalse($this->_sut->isWordPress());
    }
    
}
