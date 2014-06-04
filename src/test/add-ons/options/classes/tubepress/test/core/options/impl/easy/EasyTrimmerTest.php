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
 * @covers tubepress_core_options_impl_easy_EasyTrimmer<extended>
 */
class tubepress_test_core_options_impl_easy_EasyTrimmerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @dataProvider getData
     */
    public function testRegularTrim($charlist, $incoming, $expected, $rtrim = false, $ltrim = false)
    {
        $sut = new tubepress_core_options_impl_easy_EasyTrimmer($charlist);

        if ($ltrim) {

            $sut->setModeToLtrim();

        } else if ($rtrim) {

            $sut->setModeToRtrim();
        }

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);

        $sut->onOption($mockEvent);
        $this->assertTrue(true);
    }

    public function getData()
    {
        return array(

            array('#', '###44###', '44'),
            array('#', '###44',    '44'),
            array('#', '44###',    '44'),
            array('#', '###44###', '###44', true),
            array('#', '###44',    '###44', true),
            array('#', '44###',    '44',    true),
            array('#', '###44###', '44###', false, true),
            array('#', '###44',    '44', false, true),
            array('#', '44###',    '44###', false, true),
        );
    }
}