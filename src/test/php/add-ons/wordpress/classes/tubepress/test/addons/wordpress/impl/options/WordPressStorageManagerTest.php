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

/**
 * @covers tubepress_addons_wordpress_impl_options_WordPressStorageManager<extended>
 */
class tubepress_test_addons_wordpress_impl_options_WordPressStorageManagerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_options_WordPressStorageManager
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionValidator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public $options = 'xyz';

    public function onSetup()
    {
        global $wpdb;

        $wpdb = $this;

        $this->_mockEnvironmentDetector      = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEventDispatcher          = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockOptionValidator          = $this->createMockSingletonService(tubepress_spi_options_OptionValidator::_);
        $this->_mockOptionsReference         = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $this->_sut = new tubepress_addons_wordpress_impl_options_WordPressStorageManager();
    }

    public function onTearDown()
    {
        global $wpdb;

        unset($wpdb);
    }

    public function testGet()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->once()->with('tubepress-a')->andReturn('b');

        $result = $this->_sut->get('a');

        $this->assertEquals('b', $result);
    }

    public function testCreate()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-a', 'b');

        $this->_sut->createEachIfNotExists(array('a' => 'b'));
    }

    public function testSetDoNotPersist()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');
        $od->setDoNotPersist();

        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);

        $result = $this->_sut->set('something', 'value');

        $this->assertTrue($result);
    }

    public function testSetFailsValidation()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(false);
        $this->_mockOptionValidator->shouldReceive('getProblemMessage')->once()->with('something', 'value')->andReturn('xyz');

        $result = $this->_sut->set('something', 'value');

        $this->assertEquals('xyz', $result);
    }

    public function testSetPassesValidation()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
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

