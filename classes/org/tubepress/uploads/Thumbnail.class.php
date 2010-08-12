<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

/**
 * An uploaded video's thumbnail.
 *
 */
class org_tubepress_uploads_Thumbnail
{
    private $_videoId;
    private $_index;
    private $_height;
    private $_width;
    
    public function getHttpUri()
    {
        //TODO: implement me
    }
    
    public function getAbsoluteFilesystemPath()
    {
        //TODO: implement me
    }
    
    public function setVideoId($id)
    {
        $this->_videoId = $id;
    }
    
    public function getVideoId()
    {
        return $this->_videoId;
    }
    
    public function setIndex($index)
    {
        $this->_index = $index;
    }
    
    public function getIndex()
    {
        return $this->_index;
    }
    
    public function setHeight($height)
    {
        $this->_height = $height;
    }
    
    public function getHeight()
    {
        return $this->_height;
    }
    
    public function setWidth($width)
    {
        $this->_width = $width;
    }
    
    public function getWidth()
    {
        return $this->_width;
    }
}
