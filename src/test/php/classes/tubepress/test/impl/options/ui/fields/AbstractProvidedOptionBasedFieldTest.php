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
abstract class tubepress_test_impl_options_ui_fields_AbstractProvidedOptionBasedFieldTest extends tubepress_test_impl_options_ui_fields_AbstractTemplateBasedOptionsPageFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    public final function doMoreSetup()
    {
        $this->_mockOptionProvider = $this->createMockSingletonService(tubepress_spi_options_OptionProvider::_);
        $optionName                = $this->getOptionName();

        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with($optionName)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('getLabel')->once()->with($optionName)->andReturn('the label');
        $this->_mockOptionProvider->shouldReceive('getDescription')->once()->with($optionName)->andReturn('the description');

        $this->performAdditionalSetup();
    }

    public function testSubmitSimpleInvalid()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionName())->andReturn(false);

        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(true);
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getOptionName())->andReturn('some-value');

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($this->getOptionName(), 'some-value')->andReturn('you suck');

        $this->assertEquals('you suck', $this->getSut()->onSubmit());
    }

    public function testSubmitNoExist()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionName())->andReturn(false);

        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(false);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitBoolean()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionName())->andReturn(true);

        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(true);

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($this->getOptionName(), true)->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitSimple()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionName())->andReturn(false);

        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(true);
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getOptionName())->andReturn('some-value');

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with($this->getOptionName(), 'some-value')->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadOptionName()
    {
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with($this->getOptionName())->andReturn(false);

        new tubepress_impl_options_ui_fields_TextField(
            $this->getOptionName(), $this->getMockMessageService(),
        $this->getMockStorageManager());
    }

    public function testGetProOnlyNo()
    {
        $this->_mockOptionProvider->shouldReceive('isProOnly')->once()->with($this->getOptionName())->andReturn(false);

        $this->assertTrue($this->getSut()->isProOnly() === false);
    }

    public function testGetProOnlyYes()
    {
        $this->_mockOptionProvider->shouldReceive('isProOnly')->once()->with($this->getOptionName())->andReturn(true);

        $this->assertTrue($this->getSut()->isProOnly() === true);
    }

    protected function performAdditionalSetup()
    {
        //override point
    }

    /**
     * @return string
     */
    protected function getOptionName()
    {
        return 'name';
    }

    protected function getExpectedFieldId()
    {
        return $this->getOptionName();
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return 'the label';
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return 'the description';
    }

    protected final function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $this->getMockStorageManager()->shouldReceive('fetch')->once()->with($this->getOptionName())->andReturn('some value');

        $template->shouldReceive('setVariable')->once()->with('id', $this->getOptionName());
        $template->shouldReceive('setVariable')->once()->with('value', 'some value');

        $this->doAdditionalPrepForGetWidgetHtml($template);
    }

    protected function getMockOptionProvider()
    {
        return $this->_mockOptionProvider;
    }

    protected function doAdditionalPrepForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        //override point
    }
}
