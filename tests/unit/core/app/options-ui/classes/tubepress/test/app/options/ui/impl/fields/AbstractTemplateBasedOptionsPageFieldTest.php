<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_options_ui_impl_fields_AbstractTemplateBasedOptionsPageField
 */
abstract class tubepress_test_app_options_ui_impl_fields_AbstractTemplateBasedOptionsPageFieldTest extends tubepress_test_app_options_ui_impl_fields_AbstractOptionsPageFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplate;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    protected function onAfterOptionsPageFieldSetup()
    {
        $this->_mockEventDispatcher = $this->mock('tubepress_lib_event_api_EventDispatcherInterface');
        $this->_mockTemplate        = $this->mock('tubepress_lib_template_api_TemplateInterface');
        $this->_mockTemplateFactory = $this->mock('tubepress_lib_template_api_TemplateFactoryInterface');

        $this->onAfterTemplateBasedFieldSetup();
    }

    public function testGetWidgetHtmlWithTemplate()
    {
        $mockTemplateEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('setArgument')->once()->with('field', $this->getSut());
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockTemplate)->andReturn($mockTemplateEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_app_options_ui_api_Constants::EVENT_OPTIONS_UI_FIELD_TEMPLATE, $mockTemplateEvent);

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array($this->getExpectedTemplatePath()))->andReturn($this->_mockTemplate);

        $this->prepareForGetWidgetHtml($this->_mockTemplate);

        $this->_mockTemplate->shouldReceive('toString')->once()->andReturn('abc');

        $html = $this->getSut()->getWidgetHTML();

        $this->assertEquals('abc', $html);
    }

    /**
     * @return string
     */
    protected abstract function getExpectedTemplatePath();

    /**
     * @return void
     */
    protected abstract function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template);

    protected function onAfterTemplateBasedFieldSetup()
    {
        //override point
    }

    /**
     * @return tubepress_app_options_ui_impl_fields_AbstractTemplateBasedOptionsPageField
     */
    protected function getSut()
    {
        return parent::getSut();
    }

    protected function getMockEventDispatcher()
    {
        return $this->_mockEventDispatcher;
    }

    protected function getMockTemplateFactory()
    {
        return $this->_mockTemplateFactory;
    }
}
