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

spl_autoload_register("tubepress_classloader");

function tubepress_classloader($className) {
	
    $folder = tp_classFolder($className);
    
    if ($folder !== false) {
        include_once($folder . $className . ".class.php");
    } else {
        if (!class_exists($className, false)) {
            echo $className . " class not found <br />";
        }
    }
}
    
function tp_classFolder($className, $sub = DIRECTORY_SEPARATOR) {
    
    $currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "..";

    $dir = dir($currentDir . $sub);

    if (file_exists($currentDir.$sub.$className.".class.php")) {
        return $currentDir.$sub;
    }
    
    while (false !== ($folder = $dir->read())) {
            
        if (strpos($folder, ".") === 0) {
            continue;
        }
            
        if (is_dir($currentDir.$sub.$folder)) {
            $subFolder = tp_classFolder($className, $sub.$folder.DIRECTORY_SEPARATOR);
                    
            if ($subFolder) {
                return $subFolder;
            }
        }     
    }
    $dir->close();
    return false;
}
?>