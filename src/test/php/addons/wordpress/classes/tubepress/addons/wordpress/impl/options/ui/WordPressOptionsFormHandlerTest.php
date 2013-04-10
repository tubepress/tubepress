<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_wordpress_impl_options_ui_WordPressFormHandlerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_options_ui_WordPressOptionsFormHandler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTabs;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilter;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockTabs                = $this->createMockSingletonService(tubepress_spi_options_ui_FormHandler::_);
        $this->_mockMessageService      = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_mockFilter = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_Field');

        $this->_sut = new tubepress_addons_wordpress_impl_options_ui_WordPressOptionsFormHandler($this->_mockTabs, $this->_mockFilter);
    }


    public function testGetFailureMessagesOneError()
    {
        $this->_mockTabs->shouldReceive('onSubmit')->once()->andReturn(array('holy smokes!'));
        $this->_mockFilter->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->assertEquals(array('holy smokes!'), $this->_sut->onSubmit());
    }

    public function testOnSubmit()
    {
        $this->_mockTabs->shouldReceive('onSubmit')->once()->andReturn(null);
        $this->_mockFilter->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testGetHtml()
    {
        $template       = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/php/addons/wordpress/resources/templates/options_page.tpl.php')->andReturn($template);

        $this->_mockMessageService->shouldReceive('_')->once()->with('TubePress Options')->andReturn('<<title>>');
        $this->_mockMessageService->shouldReceive('_')->once()->with('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more information.')->andReturn('<<blurb>>');
        $this->_mockMessageService->shouldReceive('_')->once()->with('Save')->andReturn('<<save>>');

        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(false);

        $this->_mockTabs->shouldReceive('getHtml')->once()->andReturn('<<tabhtml>>');

        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE, '<<title>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO, '<<blurb>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT, '<<save>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS, '<<tabhtml>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER, $this->_mockFilter);
        $template->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_options_ui_WordPressOptionsFormHandler::TEMPLATE_VAR_BOX_ARRAY, '[{"title":"You\'re Missing Out!","url":"http:\/\/tubepress.org\/snippets\/wordpress\/youre-missing-out.php"},{"title":"TubePress News","url":"http:\/\/tubepress.org\/snippets\/wordpress\/latest-news.php"},{"title":"Need Help?","url":"http:\/\/tubepress.org\/snippets\/wordpress\/need-help.php"}]');
        $template->shouldReceive('toString')->once()->andReturn('foo');

        $this->assertEquals('foo', $this->_sut->getHtml());
    }
}