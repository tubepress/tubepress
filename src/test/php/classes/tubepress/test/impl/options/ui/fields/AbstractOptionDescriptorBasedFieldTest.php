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
abstract class tubepress_test_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest extends tubepress_test_impl_options_ui_fields_AbstractTemplateBasedOptionsPageFieldTest
{
    /**
     * @var tubepress_spi_options_OptionDescriptor
     */
    private $_mockOptionDescriptor;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionDescriptorReference;

    public final function doMoreSetup()
    {
        $this->_mockOptionDescriptorReference   = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockOptionDescriptor            = new tubepress_spi_options_OptionDescriptor($this->getOptionName());

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with($this->getOptionName())->andReturn($this->_mockOptionDescriptor);
        $this->_mockOptionDescriptor->setLabel('the label');
        $this->_mockOptionDescriptor->setDescription('the description');

        $this->performAdditionalSetup();
    }

    public function testSubmitSimpleInvalid()
    {
        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(true);
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getOptionName())->andReturn('some-value');

        $this->getMockStorageManager()->shouldReceive('set')->once()->with($this->getOptionName(), 'some-value')->andReturn('you suck');

        $this->assertEquals('you suck', $this->getSut()->onSubmit());
    }

    public function testSubmitNoExist()
    {
        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(false);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitBoolean()
    {
        $this->_mockOptionDescriptor->setBoolean();

        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(true);

        $this->getMockStorageManager()->shouldReceive('set')->once()->with($this->getOptionName(), true)->andReturn(true);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitSimple()
    {
        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with($this->getOptionName())->andReturn(true);
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with($this->getOptionName())->andReturn('some-value');

        $this->getMockStorageManager()->shouldReceive('set')->once()->with($this->getOptionName(), 'some-value')->andReturn(true);

        $this->assertNull($this->getSut()->onSubmit());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadOptionName()
    {
        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with($this->getOptionName())->andReturn(null);

        new tubepress_impl_options_ui_fields_TextField($this->getOptionName());
    }

    public function testGetProOnlyNo()
    {
        $this->assertTrue($this->getSut()->isProOnly() === false);
    }

    public function testGetProOnlyYes()
    {
        $this->_mockOptionDescriptor->setProOnly();

        $this->assertTrue($this->getSut()->isProOnly() === true);
    }

    protected function performAdditionalSetup()
    {
        //override point
    }

    /**
     * @return tubepress_spi_options_OptionDescriptor
     */
    protected function getMockOptionDescriptor()
    {
        return $this->_mockOptionDescriptor;
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
        return $this->_mockOptionDescriptor->getName();
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return $this->_mockOptionDescriptor->getLabel();
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return $this->_mockOptionDescriptor->getDescription();
    }

    protected final function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $this->getMockStorageManager()->shouldReceive('get')->once()->with($this->getOptionName())->andReturn('some value');

        $template->shouldReceive('setVariable')->once()->with('id', $this->getOptionName());
        $template->shouldReceive('setVariable')->once()->with('value', 'some value');

        $this->doAdditionalPrepForGetWidgetHtml($template);
    }

    protected function doAdditionalPrepForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        //override point
    }
}
