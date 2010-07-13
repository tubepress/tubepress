<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/shortcode/SimpleShortcodeService.class.php';

class org_tubepress_single_VideoTest extends PHPUnit_Framework_TestCase
{
	private $_provider;
    private $_template;
    private $_container;
    private $_tpom;
    private $_optionsReference;
    private $_messageService;
    private $_log;
    private $_video;
    private $_eps;
    
    private $_sut;
    	
	function setUp()
	{
		$this->_sut = new org_tubepress_single_VideoImpl();
		$this->createMocks();
		$this->applyMocks();
		$this->setupMocks();
	}
	
	function testGetHtml()
	{
		$result = $this->_sut->getSingleVideoHtml('someid');	
		$this->assertEquals('something', $result);
	}
	
	private function setupMocks()
	{
		$this->_provider->expects($this->once())
		     ->method('getSingleVideo')
		     ->will($this->returnValue($this->_video));	
		
		$this->_optionsReference->expects($this->once())
		     ->method('getOptionNamesForCategory')
		     ->with(org_tubepress_options_Category::META)
		     ->will($this->returnValue(array()));
		
		$this->_container->expects($this->once())
		     ->method('safeGet')
		     ->will($this->returnValue($this->_eps));
		     
		$this->_template->expects($this->once())
		     ->method('toString')
		     ->will($this->returnValue('something'));
	}
	
	private function applyMocks()
	{
		$this->_sut->setVideoProvider($this->_provider);
		$this->_sut->setTemplate($this->_template);
		$this->_sut->setContainer($this->_container);
		$this->_sut->setOptionsManager($this->_tpom);
		$this->_sut->setOptionsReference($this->_optionsReference);
		$this->_sut->setMessageService($this->_messageService);
	}
	
	private function createMocks()
	{
		$this->_provider = $this->getMock('org_tubepress_video_feed_provider_Provider');
		$this->_template = $this->getMock('org_tubepress_template_Template');
		$this->_container = $this->getMock('org_tubepress_ioc_IocService');
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_optionsReference = $this->getMock('org_tubepress_options_reference_OptionsReference');
		$this->_messageService = $this->getMock('org_tubepress_message_MessageService');
		$this->_log = $this->getMock('org_tubepress_log_Log');
		$this->_video = $this->getMock('org_tubepress_video_Video');
		$this->_eps = $this->getMock('org_tubepress_embedded_EmbeddedPlayerService');
	}


}
?>