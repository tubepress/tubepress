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
 * @covers tubepress_player_impl_SoloOrStaticPlayerLocation<extended>
 */
class tubepress_test_player_impl_SoloOrStaticPlayerLocationTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_player_impl_SoloOrStaticPlayerLocation
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrl;

    public function onSetup()
    {
        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockUrl        = $this->mock(tubepress_api_url_UrlInterface::_);

        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($this->_mockUrl);
        $this->_mockUrl->shouldReceive('removeSchemeAndAuthority');

        $this->_sut = new tubepress_player_impl_SoloOrStaticPlayerLocation('name', 'display name', $this->_mockUrlFactory);
    }

    public function testAnchorAttributes()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');
        $mockQuery     = $this->mock('tubepress_api_url_QueryInterface');

        $mockMediaItem->shouldReceive('getId')->once()->andReturn('abc');

        $this->_mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrl->shouldReceive('toString')->once()->andReturn('xyz');


        $mockQuery->shouldReceive('set')->once()->with('tubepress_item', 'abc');

        $expected = array(
            'rel'  => 'nofollow',
            'href' => 'xyz',
        );

        $this->assertEquals($expected, $this->_sut->getAttributesForInvocationAnchor($mockMediaItem));
    }
}

