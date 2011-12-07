<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/DefaultTabsHandler.class.php';

class org_tubepress_impl_options_ui_DefaultTabsHandlerTest extends TubePressUnitTest {

	private $_sut;
	
	private $_expectedTabs;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_ui_DefaultTabsHandler();
		
		$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
		$gal = $ioc->get(org_tubepress_impl_options_ui_tabs_GallerySourceTab::_);
		$tab = $ioc->get('org_tubepress_impl_options_ui_tabs_ThumbsTab');
		$emb = $ioc->get(org_tubepress_impl_options_ui_tabs_EmbeddedTab::_);
		$met = $ioc->get(org_tubepress_impl_options_ui_tabs_MetaTab::_);
		$fee = $ioc->get(org_tubepress_impl_options_ui_tabs_FeedTab::_);
		$cah = $ioc->get(org_tubepress_impl_options_ui_tabs_CacheTab::_);
		$adv = $ioc->get('org_tubepress_impl_options_ui_tabs_AdvancedTab');
		
		$this->_expectedTabs = array($gal, $tab, $adv, $cah, $emb, $fee, $met);
	}

	public function testSubmitWithErrors()
	{
	    $vals = array('one' => 'two');
	     
	    $x = 1;
	    foreach ($this->_expectedTabs as $tab) {
	         
	        $tab->shouldReceive('onSubmit')->once()->with($vals)->andReturn(array($x++));
	    }
	     
	    $result = $this->_sut->onSubmit($vals);
	    
	    $this->assertEquals(array(1, 2, 5, 7, 6, 4, 3), $result);
	}
	
	public function testSubmit()
	{
	    $vals = array('one' => 'two');
	    
	    foreach ($this->_expectedTabs as $tab) {
	    
	        $tab->shouldReceive('onSubmit')->once()->with($vals);
	    }
	    
	    $this->assertNull($this->_sut->onSubmit($vals));
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testSubmitNonArray()
	{
	    $this->_sut->onSubmit(3);
	}
	
	public function testGetHtml()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $tabs           = array(
            $ioc->get(org_tubepress_impl_options_ui_tabs_GallerySourceTab::_),
            $ioc->get('org_tubepress_impl_options_ui_tabs_ThumbsTab'),
            $ioc->get(org_tubepress_impl_options_ui_tabs_EmbeddedTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_MetaTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_FeedTab::_),
            $ioc->get(org_tubepress_impl_options_ui_tabs_CacheTab::_),
            $ioc->get('org_tubepress_impl_options_ui_tabs_AdvancedTab'),
        );

	    $template = \Mockery::mock(org_tubepress_api_template_Template::_);
	    $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS, $tabs);
	    $template->shouldReceive('toString')->once()->andReturn('foobar');

	    $fse            = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
	    $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

	    $tb = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
	    $tb->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/sys/ui/templates/options_page/tabs.tpl.php')->andReturn($template);

	    $this->assertEquals('foobar', $this->_sut->getHtml());
	}
}

