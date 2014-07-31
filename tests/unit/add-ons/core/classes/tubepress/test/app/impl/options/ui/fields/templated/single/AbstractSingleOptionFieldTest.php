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

abstract class tubepress_test_app_impl_options_ui_fields_templated_single_AbstractSingleOptionFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_AbstractTemplatedFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    public final function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockOptionsReference = $this->mock(tubepress_app_api_options_ReferenceInterface::_);
        $optionName                  = $this->getOptionsPageItemId();

        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with($optionName)->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()->with($optionName)->andReturn('the label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedDescription')->once()->with($optionName)->andReturn('the description');

        $this->onAfterSingleFieldSetup();
    }

    public function testSubmitSimpleInvalid()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn('some-value');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getOptionsPageItemId(), 'some-value')->andReturn('you suck');

        $this->assertEquals('you suck', $this->getSut()->onSubmit());
    }

    public function testSubmitNoExist()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitBoolean()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(true);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(true);

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getOptionsPageItemId(), true)->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitSimple()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getOptionsPageItemId())->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn('some-value');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getOptionsPageItemId(), 'some-value')->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testGetProOnlyNo()
    {
        $this->_mockOptionsReference->shouldReceive('isProOnly')->once()->with($this->getOptionsPageItemId())->andReturn(false);

        $this->assertTrue($this->getSut()->isProOnly() === false);
    }

    public function testGetProOnlyYes()
    {
        $this->_mockOptionsReference->shouldReceive('isProOnly')->once()->with($this->getOptionsPageItemId())->andReturn(true);

        $this->assertTrue($this->getSut()->isProOnly() === true);
    }

    protected function onAfterSingleFieldSetup()
    {
        //override point
    }

    protected final function getExpectedTemplateVariables()
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with($this->getOptionsPageItemId())->andReturn('some value');

        $dis = array(

            'id' => $this->getOptionsPageItemId(),
            'value' => 'some value'
        );
        $fromChild = $this->getAdditionalExpectedTemplateVariables();

        return array_merge($dis, $fromChild);
    }

    protected function getMockOptionsReference()
    {
        return $this->_mockOptionsReference;
    }

    protected function getAdditionalExpectedTemplateVariables()
    {
        return array();
    }

    protected abstract function getOptionsPageItemId();
}
