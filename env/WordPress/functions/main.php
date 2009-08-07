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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../classes/tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_storage_WordPressStorageManager',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_shortcode_SimpleShortcodeService',
    'org_tubepress_ioc_DefaultIocService',
    'org_tubepress_ioc_IocService',
    'org_tubepress_util_Debug',
    'org_tubepress_util_StringUtils'));

/**
 * Main filter hook. Looks for a tubepress tag
 * and, if found, replaces it with a gallery
*/
function tubepress_content_filter($content = '')
{
	try {
		return _tubepress_content_filter($content);
	} catch (Exception $e) {
		return $e->getMessage() . $content;
	}
}

/**
 * Enter description here...
 * 
 * @param $content
 * 
 * @return unknown_type
 */
function _tubepress_content_filter($content)
{

    /* do as little work as possible here 'cause we might not even run */
	$wpsm             = new org_tubepress_options_storage_WordPressStorageManager();
    $trigger          = $wpsm->get(org_tubepress_options_category_Advanced::KEYWORD);
	$shortcodeService = new org_tubepress_shortcode_SimpleShortcodeService();
    
	/* no shortcode? get out */
    if (!$shortcodeService->somethingToParse($content, $trigger)) {
	    return $content;
	}
    
    return _tubepress_get_gallery_content($content, $trigger, $shortcodeService);
}

/**
 * Enter description here...
 * 
 * @param $content
 * @param $trigger
 * @param org_tubepress_shortcode_ShortcodeService $shortcodeService
 * 
 * @return unknown_type
 */
function _tubepress_get_gallery_content($content, $trigger,
    org_tubepress_shortcode_ShortcodeService $shortcodeService)
{
    /* Whip up the IOC service */
    $iocContainer = new org_tubepress_ioc_DefaultIocService();
    
    /* Get a handle to our options manager */
    $tpom = $iocContainer->get(org_tubepress_ioc_IocService::OPTIONS_MGR);

 	/* Get a copy of the content that we'll edit */
    $newcontent = $content;

    /* And finally, the gallery itself */
    $gallery = $iocContainer->get(org_tubepress_ioc_IocService::GALLERY);

    while ($shortcodeService->somethingToParse($newcontent, $trigger)) {

	    $shortcodeService->parse($newcontent, $tpom);

    	if (org_tubepress_util_Debug::areWeDebugging($tpom)) {
	        org_tubepress_util_Debug::execute($iocContainer);
	    }

	    /* replace the tag with our new content */
	    $newcontent = org_tubepress_util_StringUtils::replaceFirst($tpom->getShortcode(), 
	        $gallery->generate(mt_rand()), $newcontent);
    }
    
    return $newcontent;
}

/**
 * Spits out the CSS and JS files that we need for
 * 
 * @return void
 */
function tubepress_head_filter()
{
	try {
	    _tubepress_head_filter();
	} catch (Exception $e) {
		/* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
	}
}

/**
 * Enter description here...
 * 
 * @return unknown_type
 */
function _tubepress_head_filter() {
    global $tubepress_base_url;

    print<<<GBS
<script type="text/javascript" src="$tubepress_base_url/ui/lib/tubepress.js"></script>
<link rel="stylesheet" href="$tubepress_base_url/ui/lib/tubepress.css" type="text/css" />
<script type="text/javascript">jQuery(document).ready(function() {tubepress_init("$tubepress_base_url");});</script>

GBS;
}

/**
 * Tells WordPress to load jQuery for us
 * 
 * @return void
 */
function tubepress_load_jquery()
{
    wp_enqueue_script('jquery');
}
?>
