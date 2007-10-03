<?php
/**
 * TPAbstractHasName.php
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
abstract class TPAbstractHasName
{
    private $name;

    public function __construct($theName) {
    	$this->name = (string)$theName;
    }
    
  	public function getName() {
  		return $this->name;
  	}
}
?>
