<?php
/**

tp_popup.php

The HTML associated with popup windows (and some modal windows)

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

/*
 * If someone can explain to me why I need to modify the header here,
 * and the XHTML meta tag doesn't work, I would be very grateful :)
 */
header('Content-Type: text/html;charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
                <title><?php echo stripslashes(rawurldecode($_GET['name'])); ?></title>        
        </head>
        <body style="margin: 0pt 0pt; background-color: black">
        	     <?php echo stripslashes(rawurldecode($_GET['embed'])); ?>
        </body>
</html>