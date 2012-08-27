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
class org_tubepress_impl_options_WordPressStorageManagerTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockEventDispatcher;

    private $_mockOptionValidator;

    private $_mockOptionsReference;

    private $_mockWordPressFunctionWrapper;

    public function setUp()
    {
        $this->_mockEnvironmentDetector      = \Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEventDispatcher          = \Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockOptionValidator          = \Mockery::mock(tubepress_spi_options_OptionValidator::_);
        $this->_mockOptionsReference         = \Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockWordPressFunctionWrapper = \Mockery::mock(tubepress_spi_wordpress_WordPressFunctionWrapper::_);

        $this->_sut = new tubepress_impl_wordpress_WordPressStorageManager(

            $this->_mockOptionsReference,
            $this->_mockOptionValidator,
            $this->_mockWordPressFunctionWrapper,
            $this->_mockEnvironmentDetector,
            $this->_mockEventDispatcher
        );
    }


    function testExists()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->once()->with('tubepress-something')->andReturn(false);

        $this->assertFalse($this->_sut->exists('something'));
    }


    function testGetNotExists()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->once()->with('tubepress-something')->andReturn(false);

        $result = $this->_sut->get('something');

        $this->assertTrue($result === false);
    }


    function testSet()
    {

    }

}

