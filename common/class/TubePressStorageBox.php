<?php
/**
 * TubePressStorageBox.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * 
*/
class_exists('TubePressOptionsPackage') ||
    require('options/TubePressOptionsPackage.php');
class_exists('TubePressPlayerPackage') ||
    require('players/TubePressPlayerPackage.php');
class_exists('TubePressModePackage') ||
    require('modes/TubePressModePackage.php');

class TubePressStorageBox
{
    /* this is our array of items */
    var $options, $players, $modes;
    
    /**
     * Default Constructor
     */
    function TubePressStorageBox()
    {
		$this->options = new TubePressOptionsPackage();
		$this->players = new TubePressPlayerPackage();
		$this->modes = new TubePressModePackage();
    }
    
    /**
     * Makes sure that each of its subpackages look good
     */
    function checkValidity()
    {
        $packages = array($this->options, $this->players, $this->modes);
        foreach($packages as $package) {
            if (!is_a($package, "TubePressDataPackage")) {
                return PEAR::raiseError("Your options are old!");
            }
            $test = $package->checkValidity();
            
            if (PEAR::isError(($test))) {
                return $test;
            }
        }
        return NULL;
    }
}
?>
