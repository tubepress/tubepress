<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_api_video_VideoTest extends TubePressUnitTest
{
	private $_vid;

	function setUp()
	{
		$this->_vid = new tubepress_api_video_Video();
	}

	function testSetGetAuthor()
	{
		$this->_vid->setAuthorUid('hough');
		$this->assertEquals($this->_vid->getAuthorUid(), 'hough');
	}

	function testSetGetAuthorDisplayName()
	{
		$this->_vid->setAuthorDisplayName('Eric Hough');
		$this->assertEquals($this->_vid->getAuthorDisplayName(), 'Eric Hough');
	}

	function testSetGetCategory()
	{
		$this->_vid->setCategory('Sports');
		$this->assertEquals($this->_vid->getCategory(), 'Sports');
	}

   function testSetGetCommentCount()
    {
        $this->_vid->setCommentCount(2);
        $this->assertEquals($this->_vid->getCommentCount(), 2);
    }

	function testSetGetDescription()
	{
		$this->_vid->setDescription('fake');
		$this->assertEquals($this->_vid->getDescription(), 'fake');
	}

   function testSetGetDuration()
    {
        $this->_vid->setDuration('3:12');
        $this->assertEquals($this->_vid->getDuration(), '3:12');
    }

   function testSetGetHomeUrl()
    {
        $this->_vid->setHomeUrl('http://youtube.com');
        $this->assertEquals($this->_vid->getHomeUrl('http://youtube.com'), 'http://youtube.com');
    }

	function testSetGetId()
	{
		$this->_vid->setId('ERERKJKFF');
		$this->assertEquals($this->_vid->getId(), 'ERERKJKFF');
	}

   function testSetGetKeywords()
    {
        $tags = 'one two three';
        $this->_vid->setKeywords($tags);
        $this->assertEquals($this->_vid->getKeywords(), $tags);
    }

   function testSetGetLikes()
    {
        $this->_vid->setLikesCount(564);
        $this->assertEquals($this->_vid->getLikesCount(), 564);
    }

	function testSetGetRatingAverage()
	{
		$this->_vid->setRatingAverage('4.5');
		$this->assertEquals($this->_vid->getRatingAverage(), '4.5');
	}

	function testSetGetRatingCount()
	{
		$this->_vid->setRatingCount('33000');
		$this->assertEquals($this->_vid->getRatingCount(), '33000');
	}

    function testSetGetThumbnailUrl()
    {
        $this->_vid->setThumbnailUrl('thumburl');
        $this->assertEquals($this->_vid->getThumbnailUrl(), 'thumburl');
    }

    function testSetGetTimeLastUpdated()
    {
        $this->_vid->setTimeLastUpdated('212233');
        $this->assertEquals($this->_vid->getTimeLastUpdated(), '212233');
    }

    function testSetGetTimePublished()
    {
        $this->_vid->setTimePublished('112233');
        $this->assertEquals($this->_vid->getTimePublished(), '112233');
    }

	function testSetGetTitle()
	{
		$this->_vid->setTitle('Mr. Title');
		$this->assertEquals($this->_vid->getTitle(), 'Mr. Title');
	}

	function testSetGetViewCount()
	{
		$this->_vid->setViewCount('12000');
		$this->assertEquals($this->_vid->getViewCount(), '12000');
	}
}
