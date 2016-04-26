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
 * @covers tubepress_dailymotion_impl_player_DailymotionPlayerLocation
 */
class tubepress_test_dailymotion_impl_player_DailymotionPlayerLocationTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_player_DailymotionPlayerLocation
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_dailymotion_impl_player_DailymotionPlayerLocation();
    }

    public function testBasics()
    {
        $this->assertEquals('dailymotion', $this->_sut->getName());
        $this->assertEquals('from the video\'s original Dailymotion page', $this->_sut->getUntranslatedDisplayName());
        $this->assertNull($this->_sut->getStaticTemplateName());
        $this->assertNull($this->_sut->getAjaxTemplateName());
    }

    public function testAnchor()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');

        $mockMediaItem->shouldReceive('getAttribute')->once()->with(tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL)->andReturn('home-url');

        $actual = $this->_sut->getAttributesForInvocationAnchor($mockMediaItem);

        $this->assertEquals(array(
            'rel'    => 'external nofollow',
            'target' => '_blank',
            'href'   => 'home-url',
        ), $actual);
    }
}
