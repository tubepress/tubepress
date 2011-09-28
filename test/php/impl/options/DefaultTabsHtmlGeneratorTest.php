<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/DefaultTabsHtmlGenerator.class.php';

class org_tubepress_impl_options_DefaultTabsHtmlGeneratorTest extends TubePressUnitTest {

	private $_sut;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_ui_DefaultTabsHtmlGenerator();
	}

	public function testGetHtml()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $tabs           = array(
            $ioc->get('org_tubepress_impl_options_ui_tabs_AppearanceTab')
        );

	    $template = \Mockery::mock(org_tubepress_api_template_Template::_);
	    $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_DefaultTabsHtmlGenerator::TEMPLATE_VAR_TABS, $tabs);
	    $template->shouldReceive('toString')->once()->andReturn('foobar');

	    $fse            = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
	    $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

	    $tb = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
	    $tb->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/sys/ui/templates/options_page/tabs.tpl.php')->andReturn($template);

	    $this->assertEquals('foobar', $this->_sut->getHtml());
	}
}

