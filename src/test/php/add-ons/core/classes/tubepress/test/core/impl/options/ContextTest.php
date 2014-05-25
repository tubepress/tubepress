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
 * @covers tubepress_core_impl_options_Context<extended>
 */
class tubepress_test_core_impl_options_ContextTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_options_Context
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_mockStorageManager  = $this->mock(tubepress_core_api_options_PersistenceInterface::_);
        $this->_mockOptionProvider  = $this->mock(tubepress_core_api_options_ProviderInterface::_);
        $this->_mockStringUtils     = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockLogger          = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_core_impl_options_Context(

            $this->_mockLogger,
            $this->_mockEventDispatcher,
            $this->_mockStorageManager,
            $this->_mockOptionProvider,
            $this->_mockStringUtils
        );
    }

    public function testSetGet()
    {
        $this->_setupFilters(tubepress_core_api_const_options_Names::THEME, 'crazytheme');
        $this->_setupValidationServiceToPass(tubepress_core_api_const_options_Names::THEME, 'crazytheme');

        $result = $this->_sut->set(tubepress_core_api_const_options_Names::THEME, 'crazytheme');

        $this->assertTrue($result === true);
        $this->assertEquals('crazytheme', $this->_sut->get(tubepress_core_api_const_options_Names::THEME));
    }

    public function testSetWithInvalidValue()
    {
        $this->_mockLogger->shouldReceive('error')->atLeast(1);

        $this->_setupFilters(tubepress_core_api_const_options_Names::THEME, 'crazytheme');
        $this->_setupValidationServiceToFail(tubepress_core_api_const_options_Names::THEME, 'crazytheme');

        $result = $this->_sut->set(tubepress_core_api_const_options_Names::THEME, 'crazytheme');

        $this->assertTrue($result === 'crazytheme was a bad value', var_export($result, true));
    }

    public function testGetCustomOption()
    {
        $this->_setupFilters(tubepress_core_api_const_options_Names::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToPass(tubepress_core_api_const_options_Names::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_core_api_const_options_Names::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setAll($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
        $this->assertEquals('fakeoptionvalue', $this->_sut->get(tubepress_core_api_const_options_Names::THEME));
        $this->assertEquals(1, sizeof(array_intersect(array('theme' => 'fakeoptionvalue'), $this->_sut->getAllInMemory())));
    }

    public function testGetCustomOptionWithBadValue()
    {
        $this->_mockLogger->shouldReceive('error')->atLeast(1);

        $this->_setupFilters(tubepress_core_api_const_options_Names::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToFail(tubepress_core_api_const_options_Names::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_core_api_const_options_Names::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setAll($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0] === 'fakeoptionvalue was a bad value');
    }

    public function testGetCustomOptionFallback()
    {
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with('nonexistent')->andReturn('something');

        $result = $this->_sut->get("nonexistent");

        $this->assertTrue($result === 'something');
    }

    private function _setupValidationServiceToFail($name, $value)
    {
        $this->_mockStringUtils->shouldReceive('redactSecrets')->once()->with("$value was a bad value")->andReturn($value);

        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with($name, $value)->andReturn(false);

        $this->_mockOptionProvider->shouldReceive('getProblemMessage')->once()->with($name, $value)->andReturnUsing(function ($n, $v) {

            return "$v was a bad value";
        });
    }

    private function _setupValidationServiceToPass($name, $value)
    {
        $this->_mockStringUtils->shouldReceive('redactSecrets')->once()->with($value)->andReturn($value);

        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with($name, $value)->andReturn(true);
    }

    private function _setupFilters($name, $value)
    {
        $mockPreValidationEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockPreValidationEvent->shouldReceive('getSubject')->once()->andReturn('abc');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($value, array('optionName' => $name))->andReturn($mockPreValidationEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, $mockPreValidationEvent);

        $mockPreValidationEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockPreValidationEvent->shouldReceive('getSubject')->once()->andReturn($value);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('abc')->andReturn($mockPreValidationEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$name", ehough_mockery_Mockery::type('tubepress_core_api_event_EventInterface'));
    }
}

