<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/shortcode/commands/SoloPlayerCommand.class.php';

class org_tubepress_impl_shortcode_commands_SoloPlayerCommandTest extends TubePressUnitTest
{
	private $_sut;
	private $_videoId;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_shortcode_commands_SoloPlayerCommand();
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
	    $this->setOptions(array(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_const_options_values_PlayerValue::SOLO));
            $this->_videoId = 'something';
	    $this->assertEquals('blabby', $this->_sut->execute(new org_tubepress_impl_shortcode_ShortcodeHtmlGenerationChainContext()));
	}
	
    

}
