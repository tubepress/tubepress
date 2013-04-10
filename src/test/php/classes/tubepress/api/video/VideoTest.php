<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_api_video_VideoTest extends TubePressUnitTest
{
    private $_vid;

    public function onSetup()
    {
        $this->_vid = new tubepress_api_video_Video();
    }

    public function testSetGetAuthor()
    {
        $this->_vid->setAuthorUid('hough');
        $this->assertEquals($this->_vid->getAuthorUid(), 'hough');
    }

    public function testSetGetAuthorDisplayName()
    {
        $this->_vid->setAuthorDisplayName('Eric Hough');
        $this->assertEquals($this->_vid->getAuthorDisplayName(), 'Eric Hough');
    }

    public function testSetGetCategory()
    {
        $this->_vid->setCategory('Sports');
        $this->assertEquals($this->_vid->getCategory(), 'Sports');
    }

   public function testSetGetCommentCount()
    {
        $this->_vid->setCommentCount(2);
        $this->assertEquals($this->_vid->getCommentCount(), 2);
    }

    public function testSetGetDescription()
    {
        $this->_vid->setDescription('fake');
        $this->assertEquals($this->_vid->getDescription(), 'fake');
    }

   public function testSetGetDuration()
    {
        $this->_vid->setDuration('3:12');
        $this->assertEquals($this->_vid->getDuration(), '3:12');
    }

   public function testSetGetHomeUrl()
    {
        $this->_vid->setHomeUrl('http://youtube.com');
        $this->assertEquals($this->_vid->getHomeUrl('http://youtube.com'), 'http://youtube.com');
    }

    public function testSetGetId()
    {
        $this->_vid->setId('ERERKJKFF');
        $this->assertEquals($this->_vid->getId(), 'ERERKJKFF');
    }

   public function testSetGetKeywords()
    {
        $tags = 'one two three';
        $this->_vid->setKeywords($tags);
        $this->assertEquals($this->_vid->getKeywords(), $tags);
    }

   public function testSetGetLikes()
    {
        $this->_vid->setLikesCount(564);
        $this->assertEquals($this->_vid->getLikesCount(), 564);
    }

    public function testSetGetRatingAverage()
    {
        $this->_vid->setRatingAverage('4.5');
        $this->assertEquals($this->_vid->getRatingAverage(), '4.5');
    }

    public function testSetGetRatingCount()
    {
        $this->_vid->setRatingCount('33000');
        $this->assertEquals($this->_vid->getRatingCount(), '33000');
    }

    public function testSetGetThumbnailUrl()
    {
        $this->_vid->setThumbnailUrl('thumburl');
        $this->assertEquals($this->_vid->getThumbnailUrl(), 'thumburl');
    }

    public function testSetGetTimeLastUpdated()
    {
        $this->_vid->setTimeLastUpdated('212233');
        $this->assertEquals($this->_vid->getTimeLastUpdated(), '212233');
    }

    public function testSetGetTimePublished()
    {
        $this->_vid->setTimePublished('112233');
        $this->assertEquals($this->_vid->getTimePublished(), '112233');
    }

    public function testSetGetTitle()
    {
        $this->_vid->setTitle('Mr. Title');
        $this->assertEquals($this->_vid->getTitle(), 'Mr. Title');
    }

    public function testSetGetViewCount()
    {
        $this->_vid->setViewCount('12000');
        $this->assertEquals($this->_vid->getViewCount(), '12000');
    }
}
