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
 * @covers tubepress_core_media_item_api_MediaItem
 */
class tubepress_test_core_media_item_api_MediaItemTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_item_api_MediaItem
     */
    private $_vid;

    public function onSetup()
    {
        $this->_vid = new tubepress_core_media_item_api_MediaItem('id');
    }

    public function testSetGetAuthor()
    {
        $this->_vid->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID, 'hough');
        $this->assertEquals($this->_vid->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID),'hough');
    }


}
