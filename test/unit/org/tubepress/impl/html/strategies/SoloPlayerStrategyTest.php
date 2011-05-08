<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/html/strategies/SoloPlayerStrategy.class.php';

class org_tubepress_impl_html_strategies_SoloPlayerStrategyTest extends TubePressUnitTest
{
	private $_sut;
	private $_videoId;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_html_strategies_SoloPlayerStrategy();
	}

	function getMock($className)
	{
	    $mock = parent::getMock($className);
	    
	    if ($className === 'org_tubepress_api_single_SingleVideo') {
	        $mock->expects($this->any())
	             ->method('getSingleVideoHtml')
	             ->will($this->returnValue('blabby'));
	    }
	    if ($className === 'org_tubepress_api_querystring_QueryStringService') {
	        $mock->expects($this->any())
	             ->method('getCustomVideo')
	             ->will($this->returnCallback(array($this, 'qssCallback')));
	    }
	    
	    return $mock;
	}
	
	function qssCallback()
	{
	    return $this->_videoId;
	}
	
	function testExecute()
	{
	    $this->_sut->start();
	    $this->assertEquals('blabby', $this->_sut->execute());
	}
	
    function testCanHandleTrue()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_player_Player::SOLO));
        $this->_videoId = 'somevideo';
        $this->_sut->start();
        $this->assertTrue($this->_sut->canHandle());
        $this->_sut->stop();
    }
	
    function testCanHandleFalse2()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_player_Player::SOLO));
        $this->_sut->start();
        $this->assertFalse($this->_sut->canHandle());
        $this->_sut->stop();
    }
    
    function testCanHandleFalse1()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => 'somethingelse'));
        $this->_sut->start();
        $this->assertFalse($this->_sut->canHandle());
    }

}
