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
abstract class tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest extends tubepress_impl_options_ui_fields_AbstractFieldTest
{
    private $_sut;

    private $_mockOptionDescriptor;

    private $_mockHttpRequestParameterService;

    private $_mockOptionsValidator;

    private $_mockStorageManager;

    private $_mockOptionDescriptorReference;

    private $_mockMessageService;

    private $_mockEnvironmentDetector;

    private $_mockTemplateBuilder;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockOptionDescriptor            = new tubepress_spi_options_OptionDescriptor('name');

        $this->_mockOptionDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn($this->_mockOptionDescriptor);

        $this->_mockStorageManager      = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockOptionsValidator    = $this->createMockSingletonService(tubepress_spi_options_OptionValidator::_);
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockMessageService      = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        parent::doSetup($this->_mockMessageService);

        $this->_sut = $this->_buildSut('name');
    }

    public function testSubmitSimpleInvalid()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('name')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('name')->andReturn('some-value');

        $this->_mockOptionsValidator->shouldReceive('isValid')->once()->with('name', 'some-value')->andReturn(false);
        $this->_mockOptionsValidator->shouldReceive('getProblemMessage')->once()->with('name', 'some-value')->andReturn('you suck');

        $this->assertEquals(array('you suck'), $this->_sut->onSubmit());
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

        $this->_mockOptionsValidator->shouldReceive('isValid')->once()->with('name', 'some-value')->andReturn(true);
        $this->_mockStorageManager->shouldReceive('set')->once()->with('name', 'some-value')->andReturn(true);

        $this->assertNull($this->_sut->onSubmit());
    }

    protected function getSut()
    {
        return $this->_sut;
    }

    protected function getMockOptionDescriptor()
    {
        return $this->_mockOptionDescriptor;
    }

    protected function getMockEnvironmentDetector()
    {
        return $this->_mockEnvironmentDetector;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadOptionName()
    {
        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with('name')->andReturn(null);

        $this->_sut = new tubepress_impl_options_ui_fields_TextField('name', 'someTab');
    }

    public function testGetInputHtml()
    {
        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_NAME, 'name');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE, '<<currentvalue>>');
        $template->shouldReceive('toString')->once()->andReturn('boogity');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/' . $this->getTemplatePath())->andReturn($template);

        $this->_mockStorageManager->shouldReceive('get')->once()->with('name')->andReturn('<<currentvalue>>');

        $this->_performAdditionToStringTestSetup($template);

        $this->assertEquals('boogity', $this->_sut->getHtml());
    }

    protected function _performAdditionToStringTestSetup($template)
    {
        //override point
    }

    protected function _performAdditionGetDescriptionSetup()
    {
        //override point
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
        $this->_mockOptionDescriptor->setDescription('some-desc');

        $this->_performAdditionGetDescriptionSetup();

        $this->assertTrue($this->_sut->getDescription() === '<<message: some-desc>>');
    }

    public function testGetTitle()
    {
        $this->_mockOptionDescriptor->setLabel('some-label');

        $this->assertTrue($this->_sut->getTitle() === '<<message: some-label>>');
    }

    protected abstract function getTemplatePath();

    protected abstract function _buildSut($name);
}
