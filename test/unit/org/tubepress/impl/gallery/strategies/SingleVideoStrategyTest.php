<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/gallery/strategies/SingleVideoStrategy.class.php';

class org_tubepress_impl_gallery_strategies_SingleVideoStrategyTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_gallery_strategies_SingleVideoStrategy();
	}

	function getMock($className)
	{
	    $mock = parent::getMock($className);
	    
	    if ($className === 'org_tubepress_api_single_SingleVideo') {
	        $mock->expects($this->any())
	             ->method('getSingleVideoHtml')
	             ->will($this->returnValue('blabby'));
	    }
	    
	    return $mock;
	}
	
	function testExecute()
	{
	    $this->_sut->start();
	    $this->assertEquals('blabby', $this->_sut->execute());
	}
	
    function testCanHandleTrue()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Gallery::VIDEO => 'somevideo'));
        $this->_sut->start();
        $this->assertTrue($this->_sut->canHandle());
        $this->_sut->stop();
    }
    
    function testCanHandleFalse()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Gallery::VIDEO => ''));
        $this->_sut->start();
        $this->assertFalse($this->_sut->canHandle());
    }

}
?>
