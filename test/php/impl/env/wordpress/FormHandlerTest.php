<?php

require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/FormHandler.class.php';

class org_tubepress_impl_env_wordpress_FormHandlerTest extends TubePressUnitTest {

    private $_sut;

    function setUp() {

        parent::setUp();

        $this->_sut = new org_tubepress_impl_env_wordpress_FormHandler();
    }

    function testGetHtml()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get('org_tubepress_api_message_MessageService');
        $templateBldr   = $ioc->get('org_tubepress_api_template_TemplateBuilder');
        $fse            = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $tabs           = $ioc->get('org_tubepress_spi_options_ui_TabsHtmlGenerator');
        $filter         = $ioc->get('org_tubepress_spi_options_ui_FilterHtmlGenerator');
        $template       = \Mockery::mock(org_tubepress_api_template_Template::_);

        $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

        $templateBldr->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/sys/ui/templates/wordpress/options_page.tpl.php')->andReturn($template);

        $messageService->shouldReceive('_')->once()->with('TubePress Options')->andReturn('<<title>>');
        $messageService->shouldReceive('_')->once()->with('Set default options for the plugin. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more info. An asterisk (*) next to an option indicates it\'s only available with <a href="http://tubepress.org/features">TubePress Pro</a>.')->andReturn('<<blurb>>');
        $messageService->shouldReceive('_')->once()->with('Save')->andReturn('<<save>>');

        $tabs->shouldReceive('getHtml')->once()->andReturn('<<tabhtml>>');

        $filter->shouldReceive('getHtml')->once()->andReturn('<<filterhtml>>');

        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE, '<<title>>');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO, '<<blurb>>');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT, '<<save>>');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS, '<<tabhtml>>');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER, '<<filterhtml>>');
        $template->shouldReceive('toString')->once()->andReturn('foo');

        $this->assertEquals('foo', $this->_sut->getHtml());
    }
}
