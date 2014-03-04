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
 * @covers tubepress_addons_wordpress_impl_filters_RowMeta
 */
class tubepress_test_addons_wordpress_impl_filters_RowMetaTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_filters_RowMeta
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_filters_RowMeta();

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
    }

    public function testFilter()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugin_basename')->once()->with('tubepress/tubepress.php')->andReturn('something');
        $this->_mockWordPressFunctionWrapper->shouldReceive('__')->once()->with('Settings', 'tubepress')->andReturn('orange');

        $mockEvent = ehough_mockery_Mockery::mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('x', 1, 'three'));
        $mockEvent->shouldReceive('getArgument')->once()->with('args')->andReturn(array('something'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'x', 1, 'three',
            '<a href="options-general.php?page=tubepress.php">orange</a>',
            '<a href="http://docs.tubepress.com/">Documentation</a>',
            '<a href="http://community.tubepress.com/">Support</a>',

        ));

        $this->_sut->filter($mockEvent);

        $this->assertTrue(true);
    }
}
