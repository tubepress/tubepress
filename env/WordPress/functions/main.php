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

function _tubepress_content_filter($content) {

    /* do as little work as possible here 'cause we might not even run */
	$wpsm             = new org_tubepress_options_storage_WordPressStorageManager();
    $trigger          = $wpsm->get(org_tubepress_options_category_Advanced::KEYWORD);
	$shortcodeService = new org_tubepress_shortcode_SimpleShortcodeService();
    
	/* no shortcode? get out */
    if (!$shortcodeService->somethingToParse($content, $trigger)) {
	    return $content;
	}
    
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
	        org_tubepress_util_Debug::execute($gallery, $tpom);
	    }

	    /* replace the tag with our new content */
	    $newcontent = org_tubepress_util_StringUtils::replaceFirst($tpom->getShortcode(), 
	        $gallery->generate(), $newcontent);
    }
    
    return $newcontent;
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
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

function _tubepress_head_filter() {
    global $tubepress_base_url;

    print<<<GBS
<script type="text/javascript" src="$tubepress_base_url/ui/lib/tubepress.js"></script>
<script type="text/javascript" src="$tubepress_base_url/ui/lib/jquery.includeMany-1.1.0.js"></script>
<link rel="stylesheet" href="$tubepress_base_url/ui/gallery/css/tubepress.css" type="text/css" />
<link rel="stylesheet" href="$tubepress_base_url/ui/widget/css/tubepress_widget.css" type="text/css" />
<link rel="stylesheet" href="$tubepress_base_url/ui/gallery/css/pagination.css" type="text/css" />

<script type="text/javascript">
	jQuery(document).ready(function() {
        tubepress_load_embedded_js("$tubepress_base_url");
        tubepress_load_players("$tubepress_base_url");
        tubepress_attach_listeners();
    });
</script>

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
