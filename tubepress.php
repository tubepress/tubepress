<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Displays highly configurable YouTube galleries in your posts, pages, and/or sidebar.
Author: Eric D. Hough
Version: 1.6.8-svn
Author URI: http://ehough.com

Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)

This file is part of TubePress (http://tubepress.org)

TubePress is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

TubePress is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
*/
 
function_exists("tp_classFolder")
    || require("tubepress_classloader.php");
    
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);    
    
include "env/WordPress/hooks/main.php";
include "env/WordPress/hooks/options_page.php";
include "env/WordPress/hooks/widget.php";	

?>
