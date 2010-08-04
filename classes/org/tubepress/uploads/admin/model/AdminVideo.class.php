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
 * A video.
 *
 */
class org_tubepress_uploads_admin_model_AdminVideo
{
    private $_absPath;
    private $_existingThumbnailsAbsPaths;
    private $_ymlFileAbsPath;
    
    public function setAbsPath($path) { $this->_absPath = $path; }
    public function setExistingThumbnailsAbsPaths($paths) { $this->_existingThumbnailsAbsPaths = $paths; }
    public function setYmlFileAbsPath($path) { $this->_ymlFileAbsPath = $path; }
    
    public function getAbsPath() { return $this->_absPath; }
    public function getExistingThumbnailsAbsPaths() { return $this->_existingThumbnailsAbsPaths; }
    public function getYmlFileAbsPath() { return $this->_ymlFileAbsPath; }
}
