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
abstract class tubepress_test_core_options_ui_impl_fields_provided_AbstractProvidedOptionBasedFieldTest extends tubepress_test_core_options_ui_impl_fields_AbstractTemplateBasedOptionsPageFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    public final function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockOptionProvider = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $optionName                = $this->getOptionsPageItemId();

        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with($optionName)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('getLabel')->once()->with($optionName)->andReturn('the label');
        $this->_mockOptionProvider->shouldReceive('getDescription')->once()->with($optionName)->andReturn('the description');

        $this->onAfterProvidedFieldSetup();
    }

    public function testSubmitSimpleInvalid()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn('some-value');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getOptionsPageItemId(), 'some-value')->andReturn('you suck');

        $this->assertEquals('you suck', $this->getSut()->onSubmit());
    }

    public function testSubmitNoExist()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitBoolean()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(true);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(true);

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getOptionsPageItemId(), true)->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitSimple()
    {
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn('some-value');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getOptionsPageItemId(), 'some-value')->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadOptionName()
    {
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        new tubepress_core_options_ui_impl_fields_provided_TextField(

            $this->getOptionsPageItemId(),
            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockEventDispatcher(),
            $this->getMockTemplateFactory(),
            $this->getMockOptionProvider()
        );
    }

    public function testGetProOnlyNo()
    {
        $this->_mockOptionProvider->shouldReceive('isProOnly')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->assertTrue($this->getSut()->isProOnly() === false);
    }

    public function testGetProOnlyYes()
    {
        $this->_mockOptionProvider->shouldReceive('isProOnly')->once()->with($this->getOptionsPageItemId())->andReturn(true);

        $this->assertTrue($this->getSut()->isProOnly() === true);
    }

    protected function onAfterProvidedFieldSetup()
    {
        //override point
    }

    protected final function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with($this->getOptionsPageItemId())->andReturn('some value');

        $template->shouldReceive('setVariable')->once()->with('id', $this->getOptionsPageItemId());
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
