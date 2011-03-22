<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/environment/SimpleEnvironmentDetector.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_environment_SimpleEnvironmentDetectorTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->initFakeIoc();
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
?>
