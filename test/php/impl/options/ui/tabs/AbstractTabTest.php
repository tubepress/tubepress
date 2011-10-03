<?php

abstract class org_tubepress_impl_options_ui_tabs_AbstractTabTest extends TubePressUnitTest {

	private $_sut;

    public function setup()
	{
		parent::setUp();

		$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
		$ms = $ioc->get(org_tubepress_api_message_MessageService::_);

		$ms->shouldReceive('_')->andReturnUsing( function ($key) {
		    return "<<message: $key>>";
		});

		$this->_sut = $this->_buildSut();
	}

	public function testGetName()
	{
        $this->assertEquals('<<message: ' . $this->_getRawTitle() . '>>', $this->_sut->getTitle());
	}

	public function testGetHtml()
	{
	    $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $templateBldr  = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
	    $fse           = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
	    $widgetBuilder = $ioc->get(org_tubepress_spi_options_ui_WidgetBuilder::_);

	    $expected = $this->_getWidgetArray();
	    $expectedWidgetArray = array();

	    foreach ($expected as $name => $type) {

	        $widgetBuilder->shouldReceive('build')->once()->with($name, $type)->andReturn("$name-$type");
	        $expectedWidgetArray[] = "$name-$type";
	    }


	    $template = \Mockery::mock(org_tubepress_api_template_Template::_);
	    $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_tabs_AbstractTab::TEMPLATE_VAR_WIDGETARRAY, $expectedWidgetArray);
	    $template->shouldReceive('toString')->once()->andReturn('final result');

	    $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath!>>');
	    $templateBldr->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath!>>/sys/ui/templates/options_page/tab.tpl.php')->andReturn($template);
	    $this->assertEquals('final result', $this->_sut->getHtml());
	}

	protected abstract function _getWidgetArray();

	protected abstract function _getRawTitle();

	protected abstract function _buildSut();
}