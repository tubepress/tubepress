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
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var array[]
     */
    private $_existingStoredOptions;

    /**
     * Leave this here for the tests.
     */
    public $options = 'xyz';

    public function onSetup()
    {
        global $wpdb;

        $wpdb = $this;

        $this->_mockEnvironmentDetector      = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEventDispatcher          = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockOptionProvider           = $this->createMockSingletonService(tubepress_spi_options_OptionProvider::_);
        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        $this->_sut = new tubepress_addons_wordpress_impl_options_WordPressStorageManager();
    }

    public function onTearDown()
    {
        global $wpdb;

        unset($wpdb);
    }

    public function testGet()
    {
        $this->_existingStoredOptions = array('abc123' => 'xyz789');

        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('abc123'));

        $result = $this->_sut->fetch('abc123');

        $this->assertEquals('xyz789', $result);
    }

    public function testFlushSaveQueueNoChangesNonBoolean()
    {
        $this->_existingStoredOptions = array('abc123' => 'xyz789');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.abc123', ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with('abc123', 'xyz789')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('abc123')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('abc123')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('abc123'));

        $this->_sut->queueForSave('abc123', 'xyz789');

        $this->assertNull($this->_sut->flushSaveQueue());
    }

    public function testFlushSaveQueue()
    {
        $this->_existingStoredOptions = array('name1' => 'yello', 'name2' => 'blue');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->twice()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.name1', ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.name2', ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));

        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('name1', 'name2'));
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('name1')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('name2')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('name1')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('name2')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with('name1', 'value1')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with('name2', 'value2')->andReturn(true);

        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-name1', 'value1');
        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-name2', 'value2');

        $this->_sut->queueForSave('name1', 'value1');
        $this->_sut->queueForSave('name2', 'value2');

        $this->assertNull($this->_sut->flushSaveQueue());
    }

    public function testCreate()
    {
        $this->_existingStoredOptions = array();

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-a', 'b');
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-c', 'd');

        $this->_sut->createEach(array('a' => 'b', 'c' => 'd'));
    }

    public function testSetDoNotPersist()
    {

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.something', ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('something')->andReturn(false);

        $result = $this->_sut->queueForSave('something', 'value');

        $this->assertNull($result);
    }

    public function testSetFailsValidation()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.something', ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));

        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('getProblemMessage')->once()->with('something', 'value')->andReturn('xyz');

        $result = $this->_sut->queueForSave('something', 'value');

        $this->assertEquals('xyz', $result);
    }

    public function testSetPassesValidation()
    {
        $this->_mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('something'));
        $this->_existingStoredOptions = array('something' => 'else');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.something', ehough_mockery_Mockery::type('tubepress_api_event_EventInterface'));

        $this->_mockOptionProvider->shouldReceive('isMeantToBePersisted')->once()->with('something')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('something')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(true);

        $result = $this->_sut->queueForSave('something', 'value');

        $this->assertNull($result);
    }

    public function get_results($query)
    {
        $this->assertEquals("SELECT option_name, option_value FROM xyz WHERE option_name LIKE 'tubepress-%'", $query);

        $toReturn = array();

        foreach ($this->_existingStoredOptions as $name => $value) {

            $fake = new stdClass();

            $fake->option_name  = $name;
            $fake->option_value = $value;

            $toReturn[] = $fake;
        }

        return $toReturn;
    }
}

