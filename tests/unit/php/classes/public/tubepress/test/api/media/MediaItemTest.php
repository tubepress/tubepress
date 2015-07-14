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
 * @covers tubepress_api_media_MediaItem
 */
class tubepress_test_api_media_MediaItemTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_media_MediaItem
     */
    private $_vid;

    public function onSetup()
    {
        $this->_vid = new tubepress_api_media_MediaItem('id');
    }

    public function testSetGetAuthor()
    {
        $this->_vid->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID, 'hough');
        $this->assertEquals($this->_vid->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID),'hough');
    }


}
