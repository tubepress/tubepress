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
class tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListenerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_sut                     = new tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener();
        $this->_mockMessageService      = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    public function testOnTemplate()
    {
        $this->_mockMessageService->shouldReceive('_')->once()->with('TubePress Options')->andReturn('<<title>>');
        $this->_mockMessageService->shouldReceive('_')->once()->with('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more information.')->andReturn('<<blurb>>');

        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(false);

        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_TITLE, '<<title>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultFormHandler::TEMPLATE_VAR_INTRO, '<<blurb>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener::TEMPLATE_VAR_BOX_ARRAY, '[{"title":"You\'re Missing Out!","url":"http:\/\/tubepress.org\/snippets\/wordpress\/youre-missing-out.php"},{"title":"TubePress News","url":"http:\/\/tubepress.org\/snippets\/wordpress\/latest-news.php"},{"title":"Need Help?","url":"http:\/\/tubepress.org\/snippets\/wordpress\/need-help.php"}]');

        $event = new tubepress_api_event_TubePressEvent($template);

        $this->_sut->onOptionsUiTemplate($event);

        $this->assertTrue(true);
    }
}