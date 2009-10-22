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
    'org_tubepress_util_StringUtils'));

/**
 * Main filter hook. Looks for a tubepress tag
 * and, if found, replaces it with a gallery
*/
function tubepress_content_filter($content = '')
{
    try {
        /* do as little work as possible here 'cause we might not even run */
        $wpsm             = new org_tubepress_options_storage_WordPressStorageManager();
        $trigger          = $wpsm->get(org_tubepress_options_category_Advanced::KEYWORD);
        $shortcodeService = new org_tubepress_shortcode_SimpleShortcodeService();
    
        /* no shortcode? get out */
        if (!$shortcodeService->somethingToParse($content, $trigger)) {
            return $content;
        }
        
        return _tubepress_get_gallery_content($content, $trigger);
    } catch (Exception $e) {
        return $e->getMessage() . $content;
    }
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
function _tubepress_get_gallery_content($content, $trigger)
{
    /* Whip up the IOC service */
    $iocContainer = new org_tubepress_ioc_DefaultIocService();
    
    /* Get a handle to our options manager */
    $tpom = $iocContainer->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
    
    /* Turn on logging if we need to */
    $log = $iocContainer->get(org_tubepress_ioc_IocService::LOG);
    $log->setEnabled($tpom->get(org_tubepress_options_category_Advanced::DEBUG_ON),$_GET);
    
    /* Get a handle to the shortcode service */
    $shortcodeService = $iocContainer->get(org_tubepress_ioc_IocService::SHORTCODE_SERVICE);

     /* Make a copy of the content that we'll edit */
    $newcontent = $content;

    /* And finally, the gallery itself */
    $gallery = $iocContainer->get(org_tubepress_ioc_IocService::GALLERY);

    /* Parse each shortcode one at a time */
    while ($shortcodeService->somethingToParse($newcontent, $trigger)) {

        $rand = mt_rand();
        $log->log("WordPress Main", sprintf("Starting to build gallery %s", $rand));
        
        $shortcodeService->parse($newcontent, $tpom);

        $currentShortcode = $tpom->getShortcode();
        $galleryHtml = $gallery->getHtml($rand);

        /* remove any leading/trailing <p> tags from the shortcode */
        $pattern = '/(<[P|p]>\s*)(' . preg_quote($currentShortcode, '/') . ')(\s*<\/[P|p]>)/';
        $newcontent = preg_replace($pattern, '${2}', $newcontent); 

        /* replace the shortcode with our new content */
        $newcontent = org_tubepress_util_StringUtils::replaceFirst($currentShortcode, 
            $galleryHtml, $newcontent);
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
    global $tubepress_base_url;
    
    try {
        print<<<GBS
<script type="text/javascript">function getTubePressBaseUrl(){return "$tubepress_base_url";}</script>
<script type="text/javascript" src="$tubepress_base_url/ui/lib/tubepress.js"></script>
<link rel="stylesheet" href="$tubepress_base_url/ui/lib/tubepress.css" type="text/css" />

GBS;
        if (isset($_GET['tubepress_page']) && $_GET['tubepress_page'] > 1) {
            print ('<meta name="robots" content="noindex, follow" />
');
        }
    } catch (Exception $e) {
        /* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
    }
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
