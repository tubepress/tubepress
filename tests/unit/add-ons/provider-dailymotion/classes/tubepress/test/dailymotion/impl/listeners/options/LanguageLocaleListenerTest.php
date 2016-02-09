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
 * @covers tubepress_dailymotion_impl_listeners_options_LanguageLocaleListener
 */
class tubepress_test_dailymotion_impl_listeners_options_LanguageLocaleListenerTest extends tubepress_api_test_TubePressUnitTest
{
    public function testBuild()
    {
        $mockSupplier = $this->mock('tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier');
        $mockEvent    = $this->mock('tubepress_api_event_EventInterface');

        $mockSupplier->shouldReceive('getValueMap')->once()->andReturn(array('foo' => 'bar'));
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('existing' => 'option'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(
            'existing' => 'option',
            'foo' => 'bar'
        ));

        $sut = new tubepress_dailymotion_impl_listeners_options_LanguageLocaleListener($mockSupplier);

        $sut->onAcceptableValues($mockEvent);
    }
}