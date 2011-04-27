<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
 */
/**
 * Handles generation of the HTML for an embedded player. This expects exactly 3 GET
 * paramters: embedName (the string name of the embedded player implementation),
 * video (the video ID to load), meta (true/false whether or not to include video meta info)
 */
function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../classes/tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_impl_ioc_IocContainer'));

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$qss      = $ioc->get('org_tubepress_api_querystring_QueryStringService');
$booter   = $ioc->get('org_tubepress_api_bootstrap_Bootstrapper');
$env      = $ioc->get('org_tubepress_api_environment_Detector');
$eps      = $ioc->get('org_tubepress_api_embedded_EmbeddedPlayer');
$provider = $ioc->get('org_tubepress_api_provider_Provider');

//if ($env->isWordPress()) {
    $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
    include '/Applications/MAMP/htdocs/tubepress_testing_ground/wp-blog-header.php';
//}

/* boot TubePress */
$booter->boot();

/* get the URL-encoded shortcode */
$shortcode = $qss->getShortcode($_GET);

/* video ID */
$videoId = $qss->getCustomVideo($_GET);

/* grab the video! */
$video = $provider->getSingleVideo($videoId);
if ($video == null) {
    header("Status: 404 Not Found");
    exit;
}
$title = $video->getTitle();
$embeddedHtml = rawurlencode($eps->toString($videoId));

echo "{ \"title\" : \"$title\", \"html\" : \"$embeddedHtml\" }";