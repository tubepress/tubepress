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
 * @covers tubepress_addons_coreapiservices_impl_options_Context<extended>
 */
class tubepress_test_addons_coreapiservices_options_ContextTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_coreapiservices_impl_options_Context
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

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockStorageManager  = $this->createMockSingletonService(tubepress_api_options_PersistenceInterface::_);
        $this->_mockOptionProvider  = $this->createMockSingletonService(tubepress_api_options_ProviderInterface::_);

        $this->_sut = new tubepress_addons_coreapiservices_impl_options_Context($this->_mockEventDispatcher, $this->_mockStorageManager, $this->_mockOptionProvider);
    }

    public function testSetGet()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');
        $this->_setupValidationServiceToPass(tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $result = $this->_sut->set(tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $this->assertTrue($result === true);
        $this->assertEquals('crazytheme', $this->_sut->get(tubepress_api_const_options_names_Thumbs::THEME));
    }

    public function testSetWithInvalidValue()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');
        $this->_setupValidationServiceToFail(tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $result = $this->_sut->set(tubepress_api_const_options_names_Thumbs::THEME, 'crazytheme');

        $this->assertTrue($result === 'crazytheme was a bad value', var_export($result, true));
    }

    public function testGetCustomOption()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToPass(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setAll($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
        $this->assertEquals('fakeoptionvalue', $this->_sut->get(tubepress_api_const_options_names_Thumbs::THEME));
        $this->assertEquals(1, sizeof(array_intersect(array('theme' => 'fakeoptionvalue'), $this->_sut->getAllInMemory())));
    }

    public function testGetCustomOptionWithBadValue()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToFail(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');

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
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with($name, $value)->andReturn(false);

        $this->_mockOptionProvider->shouldReceive('getProblemMessage')->once()->with($name, $value)->andReturnUsing(function ($n, $v) {

            return "$v was a bad value";
        });
    }

    private function _setupValidationServiceToPass($name, $value)
    {
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with($name, $value)->andReturn(true);
    }

    private function _setupFilters($name, $value)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$name", ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
    }
}

