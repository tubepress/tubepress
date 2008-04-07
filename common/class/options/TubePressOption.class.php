<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
class TubePressOption implements TubePressHasValue,
    TubePressHasDescription, TubePressHasName, TubePressHasTitle {
	
	const storageIdentifier = "tubepress";

    private $name;
    private $title;
    private $description;
    private $value;
	
	public function __construct($theName, $theTitle, $theDescription, $theDefault) {
		$this->name = $theName;
		$this->title = $theTitle;
		$this->description = $theDescription;
		$this->value = $theDefault;
	}

	public final function getName() { return $this->name; }
	public final function getTitle() { return $this->title; }
	public final function getDescription() { return $this->description; }
	public final function &getValue() { return $this->value; }
}
?>