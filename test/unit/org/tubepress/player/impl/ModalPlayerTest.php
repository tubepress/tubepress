<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/player/impl/ModalPlayer.class.php';

class org_tubepress_player_impl_ModalPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpeps;
	private $_tpom;
	private $_video;
	private $_ioc;
	private $_template;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_ModalPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_video = $this->getMock('org_tubepress_video_Video');
		$this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
        $this->_template = $this->getMock('org_tubepress_template_Template');
		
		$this->_sut->setContainer($this->_ioc);
		$this->_sut->setOptionsManager($this->_tpom);
		$this->_sut->setTemplate($this->_template);
	}
	
	function testGetPreGalleryHtml()
	{
		$this->_ioc->expects($this->once())
                   ->method('safeGet')
                   ->will($this->returnValue($this->_tpeps)); 
        $this->_video->expects($this->once())
                     ->method('getId')
                     ->will($this->returnValue('TTER'));
        $this->_tpeps->expects($this->once())
                     ->method('toString')
                     ->with('TTER')
                     ->will($this->returnValue('fakeembedcode'));
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnValue(10)); 
        $this->_template->expects($this->once())
                        ->method('toString');
        
        $this->_sut->getPreGalleryHtml($this->_video, 12);
	}
}
?>