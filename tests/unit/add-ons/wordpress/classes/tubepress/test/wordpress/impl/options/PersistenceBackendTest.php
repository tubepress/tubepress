<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_options_WpPersistence<extended>
 */
class tubepress_test_wordpress_impl_options_PersistenceBackendTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_options_WpPersistence
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
        $this->_mockEnvironmentDetector      = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockEventDispatcher          = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockOptionProvider           = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);

        $this->_sut = new tubepress_wordpress_impl_options_WpPersistence(

            $this->_mockWordPressFunctionWrapper
        );
    }

    public function testCreate()
    {
        $this->_existingStoredOptions = array();

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-a', 'b');
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-c', 'd');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_load_alloptions')->once()->andReturnUsing(array($this, 'get_results'));

        $this->_sut->createEach(array('a' => 'b', 'c' => 'd'));

        $this->assertTrue(true);
    }

    public function testSaveAll()
    {
        $this->_existingStoredOptions = array('a' => 'b');

        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-foo', 'bar');

        $result = $this->_sut->saveAll(array('foo' => 'bar'));

        $this->assertNull($result);
    }

    public function get_results()
    {
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

