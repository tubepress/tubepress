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
 * @covers tubepress_core_util_impl_TimeUtils
 */
class tubepress_test_core_util_impl_TimeUtilsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_util_impl_TimeUtils
     */
    private $_sut;

    public function onSetup()
    {
        $stringUtils = new tubepress_impl_util_StringUtils();
        $this->_sut = new tubepress_core_util_impl_TimeUtils($stringUtils);
    }
    
    public function testGetRelativeTimePastDecade()
    {
        $result = $this->_sut->getRelativeTime(1000000000);
        $this->assertEquals('13 years ago', $result);
    }

    public function testGetRelativeTimePast5Years()
    {
        $result = $this->_sut->getRelativeTime(1288760400);
        $this->assertEquals('4 years ago', $result);
    }

    public function testSeconds2HumanTime()
    {
        $result = $this->_sut->secondsToHumanTime(63);
        $this->assertEquals('1:03', $result);
    }

    public function testRfc3339toUnixTime()
    {
        $result = $this->_sut->rfc3339toUnixTime('1980-11-03T09:03:33.000-05:00');
        $this->assertEquals('342108213', $result);
    }
}

