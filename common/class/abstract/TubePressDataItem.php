<?php
/**
 * TubePressDataItem.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress
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
 * An "abstract" item in TubePress (a mode, option, or player)
 */
abstract class TubePressDataItem
{
    /**
     * Each data item has a title, a description, and a default value
     */
    private $_title = "";
    private $_description = "";
    protected $value = "";

    /**
     * Simple constructor
     */
    protected function TubePressDataItem($theTitle, $theDesc, $defaultValue)
    {
        $this->_description = $theDesc;
        $this->_value = $defaultValue;
        $this->_title = $theTitle;
    }
       
    /**
     * This option's visible description (e.g. "YouTube video id")
     */
    public final function getDescription() { return $this->_description; }
    
    /**
     * This option's visible title (e.g. "Video ID"")
     */
    public final function getTitle() { return $this->_title; }
    
    /**
     * This option's value (e.g. "122445")
     */
    public final function getValue() { return $this->_value; }
    
    /**
     * Set the value for this item
     */ 
    public abstract function setValue($candidate);
}
?>
