<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_wpfilter_RowMetaListener
 */
class tubepress_test_wordpress_impl_listeners_wpfilter_RowMetaListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpfilter_RowMetaListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wpfilter_RowMetaListener(

            $this->_mockWordPressFunctionWrapper
        );
    }

    public function testRowMeta()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugin_basename')->once()->with(basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('something');
        $this->_mockWordPressFunctionWrapper->shouldReceive('__')->once()->with('Settings', 'tubepress')->andReturn('orange');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('x', 1, 'three'));
        $mockEvent->shouldReceive('getArgument')->once()->with('args')->andReturn(array('something'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'x', 1, 'three',
            '<a href="options-general.php?page=tubepress.php">orange</a>',
            '<a href="http://support.tubepress.com/">Support</a>',

        ));

        $this->_sut->onFilter_row_meta($mockEvent);

        $this->assertTrue(true);
    }
}
