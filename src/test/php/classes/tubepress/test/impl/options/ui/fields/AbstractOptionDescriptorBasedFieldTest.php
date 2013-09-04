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
abstract class tubepress_test_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField
     */
    private $_sut;

    /**
     * @var tubepress_spi_options_OptionDescriptor
     */
    private $_mockOptionDescriptor;

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
    private $_mockOptionDescriptorReference;

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
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockTemplateBuilder             = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockMessageService              = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockOptionDescriptorReference   = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockOptionDescriptor            = new tubepress_spi_options_OptionDescriptor('name');

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn($this->_mockOptionDescriptor);
        $this->_mockOptionDescriptor->setLabel('the label');
        $this->_mockOptionDescriptor->setDescription('the description');

        $this->_mockMessageService->shouldReceive('_')->once()->with('the label')->andReturn('translated label');
        $this->_mockMessageService->shouldReceive('_')->once()->with('the description')->andReturn('translated description');

        $this->_sut = $this->buildSut('name');
    }

    public function testSubmitSimpleInvalid()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('name')->andReturn('some-value');

        $this->_mockStorageManager->shouldReceive('set')->once()->with('name', 'some-value')->andReturn('you suck');

        $this->assertEquals('you suck', $this->_sut->onSubmit());
    }

    public function testSubmitNoExist()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(false);

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testSubmitBoolean()
    {
        $this->_mockOptionDescriptor->setBoolean();

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);

        $this->_mockStorageManager->shouldReceive('set')->once()->with('name', true)->andReturn(true);

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testSubmitSimple()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('name')->andReturn('some-value');

        $this->_mockStorageManager->shouldReceive('set')->once()->with('name', 'some-value')->andReturn(true);

        $this->assertNull($this->_sut->onSubmit());
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadOptionName()
    {
        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn(null);

        $this->_sut = new tubepress_impl_options_ui_fields_TextField('name');
    }

    public function testGetWidgetHtml()
    {
        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with('name', 'name');
        $template->shouldReceive('setVariable')->once()->with('value', '<<currentvalue>>');
        $template->shouldReceive('toString')->once()->andReturn('boogity');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE_LOAD, ehough_mockery_Mockery::on(array($this, '__callbackVerifyIsTemplate')));

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with($this->getAbsolutePathToTemplate())->andReturn($template);

        $this->_mockStorageManager->shouldReceive('get')->once()->with('name')->andReturn('<<currentvalue>>');

        $this->setupTemplateForWidgetHTML($template);

        $this->assertEquals('boogity', $this->_sut->getWidgetHTML());
    }

    public function testGetProOnlyNo()
    {
        $this->assertTrue($this->_sut->isProOnly() === false);
    }

    public function testGetProOnlyYes()
    {
        $this->_mockOptionDescriptor->setProOnly();

        $this->assertTrue($this->_sut->isProOnly() === true);
    }

    public function testGetDescription()
    {
        $this->assertTrue($this->_sut->getTranslatedDescription() === 'translated description');
    }

    public function testGetTitle()
    {
        $this->assertTrue($this->_sut->getTranslatedDisplayName() === 'translated label');
    }

    public function __callbackVerifyIsTemplate($template)
    {
        return $template instanceof tubepress_api_event_EventInterface;
    }

    protected function setupTemplateForWidgetHTML(ehough_mockery_mockery_MockInterface $template)
    {
        //override point
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField
     */
    protected function getSut()
    {
        return $this->_sut;
    }

    /**
     * @return tubepress_spi_options_OptionDescriptor
     */
    protected function getMockOptionDescriptor()
    {
        return $this->_mockOptionDescriptor;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockMessageService()
    {
        return $this->_mockMessageService;
    }

    protected abstract function getAbsolutePathToTemplate();

    protected abstract function buildSut($name);
}
