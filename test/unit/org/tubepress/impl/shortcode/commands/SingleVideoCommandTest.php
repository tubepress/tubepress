<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/shortcode/commands/SingleVideoCommand.class.php';

class org_tubepress_impl_shortcode_commands_SingleVideoCommandTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_shortcode_commands_SingleVideoCommand();
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
	    $this->setOptions(array(org_tubepress_api_const_options_names_Output::VIDEO => 'somevideo'));
	    $this->assertEquals('blabby', $this->_sut->execute(new org_tubepress_impl_shortcode_ShortcodeHtmlGenerationChainContext()));
	}


}

