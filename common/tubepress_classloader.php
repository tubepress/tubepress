<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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

function tubepress_classloader($className)
{
    if (class_exists($className, false) || interface_exists($className, false)) {
        return;
    }
    
    $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.class.php';
    $currentDir = dirname(__FILE__) . "/../classes/";
    $absPath = $currentDir . $fileName;
    if (file_exists($absPath)) {
        include $currentDir . $fileName;    
    }
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
	spl_autoload_register("tubepress_classloader");
} else {
	function __autoload($className) {
		return tubepress_classloader($className);
	}
}
?>