<?php

require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/WordPressFormHandler.class.php';

class org_tubepress_impl_env_wordpress_WordPressFormHandlerTest extends TubePressUnitTest {

    private $_sut;

    function setUp() {

        parent::setUp();

        $this->_sut = new org_tubepress_impl_env_wordpress_WordPressFormHandler();
    }

    /**
     * @expectedException Exception
     */
    function testGetFailureMessagesNonArrayPost()
    {
        $this->_sut->onSubmit(3);
    }

    function testGetFailureMessagesOneError()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tabs   = $ioc->get(org_tubepress_impl_options_ui_DefaultTabsHandler::__);
        $filter = $ioc->get(org_tubepress_impl_options_ui_fields_FilterMultiSelectField::__);

        $postVars = array('post');

        $tabs->shouldReceive('onSubmit')->once()->with($postVars)->andReturn(array('holy smokes!'));
        $filter->shouldReceive('onSubmit')->once()->with($postVars)->andReturn(null);

        $this->assertEquals(array('holy smokes!'), $this->_sut->onSubmit($postVars));
    }

    function testOnSubmit()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tabs   = $ioc->get(org_tubepress_impl_options_ui_DefaultTabsHandler::__);
        $filter = $ioc->get(org_tubepress_impl_options_ui_fields_FilterMultiSelectField::__);

        $postVars = array('post');

        $tabs->shouldReceive('onSubmit')->once()->with($postVars)->andReturn(null);
        $filter->shouldReceive('onSubmit')->once()->with($postVars)->andReturn(null);

        $this->assertNull($this->_sut->onSubmit($postVars));
    }

    function testGetHtml()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
        $templateBldr   = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse            = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $tabs           = $ioc->get(org_tubepress_impl_options_ui_DefaultTabsHandler::__);
        $filter         = $ioc->get(org_tubepress_impl_options_ui_fields_FilterMultiSelectField::__);
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
