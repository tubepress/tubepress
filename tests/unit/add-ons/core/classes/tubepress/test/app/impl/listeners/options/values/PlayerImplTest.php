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
 * @covers tubepress_app_impl_listeners_options_values_PlayerImpl
 */
class tubepress_test_app_impl_listeners_options_values_PlayerImplTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_options_values_PlayerImpl
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_app_impl_listeners_options_values_PlayerImpl();
    }

    public function testAcceptableValues()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'foo' => 'bar'
        ));

        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'provider_based' => 'Provider default',
            'foo'            => 'bar',
        ));

        $this->_sut->onAcceptableValues($mockEvent);

        $this->assertTrue(true);
    }
}