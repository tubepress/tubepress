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

/*
 * If someone can explain to me why I need to modify the header here,
 * and the XHTML meta tag doesn't work, I would be very grateful :)
 * 
 * Thanks to Numline1 for reporting the XSS hole on this page!
 */
header('Content-Type: text/html;charset=utf-8');

require dirname(__FILE__) . "/../../common/tubepress_classloader.php";

$urlBuilder = new org_tubepress_url_SimpleUrlBuilder();
$url = $urlBuilder->buildSingleVideoUrl($_GET["id"]);

$frs = new org_tubepress_gdata_retrieval_HTTPRequest2();
$xml = $frs->fetch($url, false);

$factory = new org_tubepress_video_factory_SimpleVideoFactory();
$vid = $factory->dom2TubePressVideoArray($xml, 1);

$embed = new org_tubepress_video_embed_SimpleEmbeddedPlayerService();
$embed->applyOptionsFromPackedString($_GET["opts"]);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
                <title><?php echo $vid[0]->getTitle(); ?></title>        
        </head>
        <body style="margin: 0pt 0pt; background-color: black">
                 <?php echo $embed->toString(); ?>
        </body>
</html>
