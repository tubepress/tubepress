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
class tubepress_impl_options_WordPressStorageManagerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockEventDispatcher;

    private $_mockOptionValidator;

    private $_mockOptionsReference;

    private $_mockWordPressFunctionWrapper;

    public $options = 'xyz';

    public function onSetup()
    {
        global $wpdb;

        $wpdb = $this;

        $this->_mockEnvironmentDetector      = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEventDispatcher          = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
        $this->_mockOptionValidator          = $this->createMockSingletonService(tubepress_spi_options_OptionValidator::_);
        $this->_mockOptionsReference         = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);

        $this->_sut = new tubepress_plugins_wordpress_impl_options_WordPressStorageManager();
    }

    function onTearDown()
    {
        global $wpdb;

        unset($wpdb);
    }

    function testSetDoNotPersist()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');
        $od->setDoNotPersist();

        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);

        $result = $this->_sut->set('something', 'value');

        $this->assertTrue($result);
    }

    function testSetFailsValidation()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET, \Mockery::type('tubepress_api_event_TubePressEvent'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(false);
        $this->_mockOptionValidator->shouldReceive('getProblemMessage')->once()->with('something', 'value')->andReturn('xyz');

        $result = $this->_sut->set('something', 'value');

        $this->assertEquals('xyz', $result);
    }

    function testSetPassesValidation()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET, \Mockery::type('tubepress_api_event_TubePressEvent'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(true);
        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-something', 'value');

        $result = $this->_sut->set('something', 'value');

        $this->assertTrue($result);
    }

    public function get_results($query)
    {
        $this->assertEquals("SELECT option_name FROM xyz WHERE option_name LIKE 'tubepress-%'", $query);

        $fake = new stdClass();

        $fake->option_name = 'abc123';

        return array($fake);
    }
}

