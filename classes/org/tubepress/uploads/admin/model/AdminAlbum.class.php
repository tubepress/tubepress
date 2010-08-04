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
 * A video album
 *
 */
class org_tubepress_uploads_admin_model_AdminAlbum
{
    private $_relativeContainerPath;
    private $_relativeVideoPaths;
    
    public function setRelativeContainerPath($id) { $this->_relativeContainerPath = $id; }
    public function setRelativeVideoPaths($videos) { $this->_relativeVideoPaths = $videos; }
    
    public function getRelativeContainerPath() { return $this->_relativeContainerPath; }
    public function getRelativeVideoPaths() { return $this->_relativeVideoPaths; }
    
}
