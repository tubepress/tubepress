<?php
/**

tp_popup.php

The HTML associated with popup windows

Copyright (C) 2007 Eric D. Hough (http://ehough.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title><?php echo urldecode($_GET['name']); ?></title>	
	</head>
	<body style="margin: 0pt 0pt">
		<object type="application/x-shockwave-flash" style="width:<?php echo $_GET['w']; ?>px; height:<?php echo $_GET['h'];?>px;" data="http://www.youtube.com/v/<?php echo $_GET['id']; ?>" >
				<param name="movie" value="http://www.youtube.com/v/<?php echo $_GET['id']; ?>" />
		</object>
	</body>
</html>