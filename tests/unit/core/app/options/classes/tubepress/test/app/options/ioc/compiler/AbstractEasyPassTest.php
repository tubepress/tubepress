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

abstract class tubepress_test_app_options_ioc_compiler_AbstractEasyPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ioc_compiler_AbstractEasyPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_sut             = $this->buildSut();
        $this->_mockContainer   = $this->mock('tubepress_platform_api_ioc_ContainerBuilderInterface');
        $this->_mockStringUtils = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);
        $this->_mockLangUtils   = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockLogger      = $this->mock('tubepress_platform_impl_log_BootLogger');

        $this->_mockContainer->shouldReceive('get')->once()->with(tubepress_platform_api_util_StringUtilsInterface::_)->andReturn($this->_mockStringUtils);
        $this->_mockContainer->shouldReceive('get')->once()->with(tubepress_platform_api_util_LangUtilsInterface::_)->andReturn($this->_mockLangUtils);
        $this->_mockContainer->shouldReceive('get')->once()->with('tubepress_platform_impl_log_BootLogger')->andReturn($this->_mockLogger);

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
    }

    public function testUnexpectedAttribute()
    {
        $paramValue = array();
        foreach ($this->getRequiredAttributes() as $requiredAttributeName) {

            $paramValue[$requiredAttributeName] = 'foo';
        }

        $paramValue['bar'] = 'bla';

        $this->_mockLogger->shouldReceive('error')->once()->with('foo has unexpected attribute: bar');

        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array('foo'));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('foo', strtolower($this->getPrefix()))->andReturn(true);
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('foo')->andReturn($paramValue);
        $this->_mockLangUtils->shouldReceive('isSimpleArrayOfStrings')->once()->with(array_keys($paramValue))->andReturn(true);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testMissingRequired()
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array('foo'));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('foo', strtolower($this->getPrefix()))->andReturn(true);
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('foo')->andReturn(array('bar'));
        $this->_mockLangUtils->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('bar'))->andReturn(true);
        $this->_mockLogger->shouldReceive('error')->once()->with('foo is missing required attributes');

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testNonStringKeys()
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array('foo'));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('foo', strtolower($this->getPrefix()))->andReturn(true);
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('foo')->andReturn(array());
        $this->_mockLangUtils->shouldReceive('isSimpleArrayOfStrings')->once()->with(array())->andReturn(false);
        $this->_mockLogger->shouldReceive('error')->once()->with('foo is not an array with string keys');

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testNonArrayValue()
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array('foo'));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('foo', strtolower($this->getPrefix()))->andReturn(true);
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('foo')->andReturn(new stdClass());
        $this->_mockLogger->shouldReceive('error')->once()->with('foo has a non-array for its value.');

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testCreateNoMatchingIds2()
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array(strtolower($this->getPrefix())));
        $this->_mockLogger->shouldReceive('error')->once()->with('Found a parameter that exactly matches the prefix ' . strtolower($this->getPrefix()));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with(strtolower($this->getPrefix()), strtolower($this->getPrefix()))->andReturn(true);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testCreateNoMatchingIds1()
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array('foo'));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('foo', strtolower($this->getPrefix()))->andReturn(false);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testCreateNoParams()
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array());

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    protected function prepareForProcessing(array $paramValue)
    {
        $this->_mockContainer->shouldReceive('getParameterNames')->once()->andReturn(array('foo'));
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('foo', strtolower($this->getPrefix()))->andReturn(true);
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('foo')->andReturn($paramValue);
        $this->_mockLangUtils->shouldReceive('isSimpleArrayOfStrings')->once()->with(array_keys($paramValue))->andReturn(true);
    }

    protected function doProcess()
    {
        $this->_sut->process($this->_mockContainer);
        $this->assertTrue(true);
    }

    /**
     * @return \ehough_mockery_mockery_MockInterface
     */
    public function getMockContainer()
    {
        return $this->_mockContainer;
    }

    /**
     * @return \ehough_mockery_mockery_MockInterface
     */
    public function getMockLangUtils()
    {
        return $this->_mockLangUtils;
    }

    /**
     * @return \ehough_mockery_mockery_MockInterface
     */
    public function getMockLogger()
    {
        return $this->_mockLogger;
    }

    /**
     * @return \ehough_mockery_mockery_MockInterface
     */
    public function getMockStringUtils()
    {
        return $this->_mockStringUtils;
    }

    protected abstract function getPrefix();

    protected abstract function getRequiredAttributes();

    protected abstract function buildSut();
}