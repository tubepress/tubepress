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
        $result = $this->_sut->fetch('abc123');

        $this->assertEquals('xyz789', $result);
    }

    public function testFlushSaveQueue()
    {
        $od1 = new tubepress_spi_options_OptionDescriptor('name1');
        $od2 = new tubepress_spi_options_OptionDescriptor('name2');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->twice()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('name1', 'value1')->andReturn(true);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('name2', 'value2')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('findOneByName')->once()->with('name1')->andReturn($od1);
        $this->_mockOptionsReference->shouldReceive('findOneByName')->once()->with('name2')->andReturn($od2);
        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-name1', 'value1');
        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-name2', 'value2');

        $this->_sut->queueForSave('name1', 'value1');
        $this->_sut->queueForSave('name2', 'value2');

        $this->assertNull($this->_sut->flushSaveQueue());
    }

    public function testCreate()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-a', 'b');
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-c', 'd');

        $this->_sut->createEachIfNotExists(array('a' => 'b', 'c' => 'd'));
    }

    public function testSetDoNotPersist()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');
        $od->setDoNotPersist();

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);

        $result = $this->_sut->queueForSave('something', 'value');

        $this->assertNull($result);
    }

    public function testSetFailsValidation()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(false);
        $this->_mockOptionValidator->shouldReceive('getProblemMessage')->once()->with('something', 'value')->andReturn('xyz');

        $result = $this->_sut->queueForSave('something', 'value');

        $this->assertEquals('xyz', $result);
    }

    public function testSetPassesValidation()
    {
        $od = new tubepress_spi_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(true);

        $result = $this->_sut->queueForSave('something', 'value');

        $this->assertNull($result);
    }

    public function get_results($query)
    {
        $this->assertEquals("SELECT option_name, option_value FROM xyz WHERE option_name LIKE 'tubepress-%'", $query);

        $fake = new stdClass();

        $fake->option_name = 'abc123';
        $fake->option_value = 'xyz789';

        return array($fake);
    }
}

