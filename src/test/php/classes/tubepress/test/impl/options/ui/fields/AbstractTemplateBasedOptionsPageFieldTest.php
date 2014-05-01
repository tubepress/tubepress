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
 *
 */
abstract class tubepress_test_impl_options_ui_fields_AbstractTemplateBasedOptionsPageFieldTest extends tubepress_test_impl_options_ui_fields_AbstractOptionsPageFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplate;

    protected final function doOnSetup()
    {
        $this->_mockTemplateBuilder = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEventDispatcher = $this->createMockSingletonService('tubepress_api_event_EventDispatcherInterface');
        $this->_mockTemplate        = $this->createMockSingletonService('ehough_contemplate_api_Template');

        $this->doMoreSetup();
    }

    public function testGetWidgetHtmlWithTemplate()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_const_event_EventNames::OPTIONS_PAGE_FIELDTEMPLATE, ehough_mockery_Mockery::on(array($this, '__verifyTemplateEvent')));

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with($this->getExpectedTemplatePath())->andReturn($this->_mockTemplate);

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

    public function __verifyTemplateEvent($event)
    {
        return $event instanceof tubepress_api_event_EventInterface && $event->getSubject() instanceof ehough_contemplate_api_Template;
    }

    protected function doMoreSetup()
    {
        //override point
    }

    protected function getMockEventDispatcher()
    {
        return $this->_mockEventDispatcher;
    }
}
