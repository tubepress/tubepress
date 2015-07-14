<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
        $this->_mockOptionsReference = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $optionName                  = $this->getId();

        $this->_mockOptionsReference->shouldReceive('optionExists')->atLeast(1)->with($optionName)->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->atLeast(1)->with($optionName)->andReturn('the label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedDescription')->atLeast(1)->with($optionName)->andReturn('the description');

        $this->onAfterSingleFieldSetup();
    }

    public function testSubmitSimpleInvalid()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getId())->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getId())->andReturn('some-value');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getId(), 'some-value')->andReturn('you suck');

        $this->assertEquals('you suck', $this->getSut()->onSubmit());
    }

    public function testSubmitNoExist()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getId())->andReturn(false);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitBoolean()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getId())->andReturn(true);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getId())->andReturn(true);

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getId(), true)->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testSubmitSimple()
    {
        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with($this->getId())->andReturn(false);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with($this->getId())->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getId())->andReturn('some-value');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with($this->getId(), 'some-value')->andReturn(null);

        $this->assertNull($this->getSut()->onSubmit());
    }

    public function testGetProOnlyNo()
    {
        $this->_mockOptionsReference->shouldReceive('isProOnly')->once()->with($this->getId())->andReturn(false);

        $this->assertTrue($this->getSut()->isProOnly() === false);
    }

    public function testGetProOnlyYes()
    {
        $this->_mockOptionsReference->shouldReceive('isProOnly')->once()->with($this->getId())->andReturn(true);

        $this->assertTrue($this->getSut()->isProOnly() === true);
    }

    public function testCloneForMultiSource()
    {
        $sut = $this->getSut();

        if ($sut instanceof tubepress_api_options_ui_MultiSourceFieldInterface) {

            $mockPersistence = $this->mock(tubepress_api_options_PersistenceInterface::_);
            $actual          = $sut->cloneForMultiSource('xyz-456-', $mockPersistence);
            $sutClass        = get_class($sut);

            $this->assertInstanceOf($sutClass, $actual);

            $this->assertNotSame($sut, $actual);
        }

        $this->assertTrue(true);
    }

    protected function onAfterSingleFieldSetup()
    {
        //override point
    }

    protected final function getExpectedTemplateVariables()
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with($this->getId())->andReturn('some value');

        $dis = array(

            'id'     => $this->getId(),
            'value'  => 'some value',
            'prefix' => '',
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

    protected abstract function getId();
}
