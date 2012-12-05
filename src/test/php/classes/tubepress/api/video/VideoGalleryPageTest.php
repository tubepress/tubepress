<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_api_video_VideoGalleryPageTest extends TubePressUnitTest
{
    private $_sut;

    function onSetup()
    {
        $this->_sut = new tubepress_api_video_VideoGalleryPage();
    }

    function testSetNonIntegralTotal()
    {
        $this->_sut->setTotalResultCount('50.1');
        $this->assertEquals(50, $this->_sut->getTotalResultCount());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetNonNumericTotal()
    {
        $this->_sut->setTotalResultCount('something bad');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetNegativeTotal()
    {
        $this->_sut->setTotalResultCount(-1501);
    }

    function testSetGetVideos()
    {
        $vids = array('hello');
        $this->_sut->setVideos($vids);
        $this->assertEquals($vids, $this->_sut->getVideos());
    }

    function testSetGetTotal()
    {
        $this->_sut->setTotalResultCount(501);
        $this->assertEquals(501, $this->_sut->getTotalResultCount());
    }
}

