<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_util_impl_TimeUtils
 */
class tubepress_test_util_impl_TimeUtilsTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_util_impl_TimeUtils
     */
    private $_sut;

    public function onSetup()
    {
        $stringUtils = new tubepress_util_impl_StringUtils();
        $this->_sut = new tubepress_util_impl_TimeUtils($stringUtils);
    }
    
    public function testGetRelativeTimePastDecade()
    {
        $result = $this->_sut->getRelativeTime(1000000000);
        $this->assertEquals('14 years ago', $result);
    }

    public function testGetRelativeTimePast5Years()
    {
        $result = $this->_sut->getRelativeTime(1288760400);
        $this->assertEquals('5 years ago', $result);
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

    /**
     * @runInSeparateProcess
     */
    public function testHumanTimeDate()
    {
        date_default_timezone_set('America/New_York');
        $result = $this->_sut->unixTimeToHumanReadable(342108202, 'l jS \of F Y h:i:s A e', false);
        $this->assertEquals('Monday 3rd of November 1980 09:03:22 AM America/New_York', $result);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHumanTimeStrftime()
    {
        date_default_timezone_set('America/New_York');

        if (setlocale(LC_TIME, 'es_ES') !== 'es_ES') {

            $this->markTestSkipped('Missing es_ES locale');
            return;
        };

        $result = $this->_sut->unixTimeToHumanReadable(342108202, '%A%e de %B, %Y, %H:%M:%S %Z', false);
        $this->assertEquals('lunes 3 de noviembre, 1980, 09:03:22 EST', $result);
    }
}

