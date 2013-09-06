<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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
abstract class tubepress_test_impl_options_ui_fields_AbstractOptionsPageFieldTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

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

    public final function onSetup()
    {
        $this->_mockMessageService              = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockTemplateBuilder             = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEventDispatcher             = $this->createMockSingletonService('tubepress_api_event_EventDispatcherInterface');
        $this->_mockTemplate                    = $this->createMockSingletonService('ehough_contemplate_api_Template');

        $this->doOnSetup();

        $this->_sut = $this->buildSut();
    }

    public function testGetId()
    {
        $result = $this->_sut->getId();

        $this->assertEquals($this->getExpectedFieldId(), $result);
    }

    public function testGetLabel()
    {
        $expected = '<<' . $this->getExpectedUntranslatedFieldLabel() . '>>';

        $this->_mockMessageService->shouldReceive('_')->once()->with($this->getExpectedUntranslatedFieldLabel())->andReturn($expected);

        $result = $this->_sut->getTranslatedDisplayName();

        $this->assertEquals($expected, $result);
    }

    public function testGetDescription()
    {
        $expected = $this->getExpectedUntranslatedFieldDescription();

        if ($expected) {

            $expected = "<<$expected>>";
            $this->_mockMessageService->shouldReceive('_')->once()->with($this->getExpectedUntranslatedFieldDescription())->andReturn($expected);

        } else {

            $expected = '';
        }

        $this->prepareForGetDescription();

        $result = $this->_sut->getTranslatedDescription();

        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetHtml()
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
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected function getSut()
    {
        return $this->_sut;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockHttpRequestParameterService()
    {
        return $this->_mockHttpRequestParameterService;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockStorageManager()
    {
        return $this->_mockStorageManager;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockMessageService()
    {
        return $this->_mockMessageService;
    }

    protected function doOnSetup()
    {
        //override point
    }

    protected function prepareForGetDescription()
    {
        //override point
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected abstract function buildSut();

    /**
     * @return string
     */
    protected abstract function getExpectedTemplatePath();

    /**
     * @return void
     */
    protected abstract function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template);

    protected abstract function getExpectedFieldId();

    protected abstract function getExpectedUntranslatedFieldLabel();

    protected abstract function getExpectedUntranslatedFieldDescription();

    public function __verifyTemplateEvent($event)
    {
        return $event instanceof tubepress_api_event_EventInterface && $event->getSubject() instanceof ehough_contemplate_api_Template;
    }
}
