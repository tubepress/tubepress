<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_exec_MemoryExecutionContextTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockStorageManager;

    private $_mockEventDispatcher;

    private $_mockValidationService;

    public function setup()
    {
        $this->_mockEventDispatcher   = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockStorageManager    = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockValidationService = Mockery::mock(tubepress_spi_options_OptionValidator::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionValidator($this->_mockValidationService);

        $this->_sut = new tubepress_impl_context_MemoryExecutionContext();
    }

    public function tearDown()
    {
        Mockery::close();
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

    public function testToShortcode()
    {
        $customOptions = array(
            tubepress_api_const_options_names_Thumbs::THEME => 'some "option" with double quotes',
            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION => 'true',
        );

        $this->_mockStorageManager->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger');

        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'some "option" with double quotes');
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION, 'true');
        $this->_setupValidationServiceToPass(tubepress_api_const_options_names_Thumbs::THEME, 'some "option" with double quotes');
        $this->_setupValidationServiceToPass(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION, 'true');

        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals('[trigger theme="some \"option\" with double quotes", ajaxPagination="true"]', $this->_sut->toShortcode());
    }

    public function testReset()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToPass(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');
        $this->_sut->setCustomOptions($customOptions);

        $this->assertEquals(array('theme' => 'fakeoptionvalue'), $this->_sut->getCustomOptions());

        $this->_sut->reset();

        $this->assertEquals(array(), $this->_sut->getCustomOptions());
    }

    public function testGetSetShortcode()
    {
        $this->_sut->setActualShortcodeUsed("fakeshort");
        $this->assertEquals("fakeshort", $this->_sut->getActualShortcodeUsed());
    }

    public function testGetCustomOption()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToPass(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setCustomOptions($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
        $this->assertEquals('fakeoptionvalue', $this->_sut->get(tubepress_api_const_options_names_Thumbs::THEME));
        $this->assertEquals(1, sizeof(array_intersect(array('theme' => 'fakeoptionvalue'), $this->_sut->getCustomOptions())));
    }

    public function testGetCustomOptionWithBadValue()
    {
        $this->_setupFilters(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');
        $this->_setupValidationServiceToFail(tubepress_api_const_options_names_Thumbs::THEME, 'fakeoptionvalue');

        $customOptions = array(tubepress_api_const_options_names_Thumbs::THEME => 'fakeoptionvalue');

        $result = $this->_sut->setCustomOptions($customOptions);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0] === 'fakeoptionvalue was a bad value');
    }

    public function testGetCustomOptionFallback()
    {
        $this->_mockStorageManager->shouldReceive('get')->once()->with('nonexistent')->andReturn('something');

        $result = $this->_sut->get("nonexistent");

        $this->assertTrue($result === 'something');
    }

    private function _setupValidationServiceToFail($name, $value)
    {
        $this->_mockValidationService->shouldReceive('isValid')->once()->with($name, $value)->andReturn(false);

        $this->_mockValidationService->shouldReceive('getProblemMessage')->once()->with($name, $value)->andReturnUsing(function ($n, $v) {

            return "$v was a bad value";
        });
    }

    private function _setupValidationServiceToPass($name, $value)
    {
        $this->_mockValidationService->shouldReceive('isValid')->once()->with($name, $value)->andReturn(true);
    }

    private function _setupFilters($name, $value)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_PreValidationOptionSet::EVENT_NAME, \Mockery::type('tubepress_api_event_PreValidationOptionSet'));
    }
}

