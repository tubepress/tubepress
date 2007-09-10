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
 * This is meant to be an abstract class, though PHP 4 doesn't support
 * them :(. The idea here is that each implementation (WordPress, MoveableType)
 * extends this class and passes it around as the class that holds all 
 * of the users options. It's essentially just an array of TubePressOptions 
 * with some extra methods related to metadata on those options.
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
     * Default options
     */
    function TubePressStorageBox()
    {
		$this->options = new TubePressOptionsPackage();
		$this->players = new TubePressPlayerPackage();
		$this->modes = new TubePressModePackage();
    }
    
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
