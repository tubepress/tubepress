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
 * @covers tubepress_app_media_item_impl_easy_EasyAttributeFormatter<extended>
 */
class tubepress_test_app_media_item_impl_easy_EasyAttributeFormatterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_media_item_impl_easy_EasyAttributeFormatter
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTimeUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaItem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider;

    public function onSetup()
    {
        $this->_mockTimeUtils     = $this->mock(tubepress_lib_util_api_TimeUtilsInterface::_);
        $this->_mockContext       = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockEvent         = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockMediaProvider = $this->mock(tubepress_app_media_provider_api_MediaProviderInterface::_);
        $this->_mockMediaItem     = $this->mock('tubepress_app_media_item_api_MediaItem');

        $this->_sut = new tubepress_app_media_item_impl_easy_EasyAttributeFormatter(
            $this->_mockTimeUtils,
            $this->_mockContext
        );
    }

    public function testFormatDates()
    {
        $this->_setupForRun('abc', 'abc');
        $this->_setupAttributes('source value', 'dest value');

        $this->_sut->formatDateFromUnixTime('source', 'dest');

        $this->_mockTimeUtils->shouldReceive('unixTimeToHumanReadable')->once()->with('source value', 'date format', true)->andReturn('dest value');

        $this->_sut->onNewMediaItem($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testTruncate()
    {
        $this->_setupForRun('abc', 'abc');
        $this->_setupAttributes('source value', 'so...');

        $this->_sut->truncateString('source', 'dest', tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT)->andReturn(2);

        $this->_sut->onNewMediaItem($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testNumbers1()
    {
        $this->_setupForRun('abc', 'abc');
        $this->_setupAttributes('44321', '44,321');

        $this->_sut->formatNumber('source', 'dest', 0);

        $this->_sut->onNewMediaItem($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testNumbers2()
    {
        $this->_setupForRun('abc', 'abc');
        $this->_setupAttributes(44321.123456789, '44,321.123');

        $this->_sut->formatNumber('source', 'dest', 3);

        $this->_sut->onNewMediaItem($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testFormatDuration()
    {
        $this->_setupForRun('abc', 'abc');
        $this->_setupAttributes('source value', 'dest value');

        $this->_sut->formatDurationFromSeconds('source', 'dest');
        $this->_mockTimeUtils->shouldReceive('secondsToHumanTime')->once()->with('source value')->andReturn('dest value');

        $this->_sut->onNewMediaItem($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testFormatWrongProvider()
    {
        $this->_setupForRun('syz', 'abc');

        $this->_sut->onNewMediaItem($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _setupAttributes($sourceValue, $expectedFinalValue)
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_media_item_api_Constants::OPTION_DATEFORMAT)->andReturn('date format');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_media_item_api_Constants::OPTION_RELATIVE_DATES)->andReturn(true);
        $this->_mockMediaItem->shouldReceive('hasAttribute')->once()->with('source')->andReturn(true);
        $this->_mockMediaItem->shouldReceive('getAttribute')->once()->with('source')->andReturn($sourceValue);
        $this->_mockMediaItem->shouldReceive('setAttribute')->once()->with('dest', $expectedFinalValue);
    }

    private function _setupForRun($expectedProviderName, $actualProviderName)
    {
        $this->_sut->setProviderName($expectedProviderName);

        $this->_mockMediaProvider->shouldReceive('getName')->once()->andReturn($actualProviderName);
        $this->_mockMediaItem->shouldReceive('getAttribute')->once()->with(tubepress_app_media_item_api_Constants::ATTRIBUTE_PROVIDER)
            ->andReturn($this->_mockMediaProvider);
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockMediaItem);
    }
}