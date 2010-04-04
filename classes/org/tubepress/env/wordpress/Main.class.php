<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_storage_WordPressStorageManager',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_shortcode_SimpleShortcodeService',
    'org_tubepress_ioc_DefaultIocService',
    'org_tubepress_ioc_ProInWordPressIocService',
    'org_tubepress_ioc_IocService',
    'org_tubepress_util_StringUtils',
    'org_tubepress_gallery_TubePressGalleryImpl'));

class org_tubepress_env_wordpress_Main
{
    public static function contentFilter($content = '')
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
        
            return org_tubepress_env_wordpress_Main::_getGalleryHtml($content, $trigger);
        } catch (Exception $e) {
            return $e->getMessage() . $content;
        }
    }

    private static function _getGalleryHtml($content, $trigger)
    {
        /* Whip up the IOC service */
        if (class_exists('org_tubepress_ioc_ProInWordPressIocService')) {
            $iocContainer = new org_tubepress_ioc_ProInWordPressIocService();
        } else {
            $iocContainer = new org_tubepress_ioc_DefaultIocService();
        }
        
        /* Get a handle to our options manager */
        $tpom = $iocContainer->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        /* Turn on logging if we need to */
        $log = $iocContainer->get(org_tubepress_ioc_IocService::LOG);
        $log->setEnabled($tpom->get(org_tubepress_options_category_Advanced::DEBUG_ON),$_GET);
        
        /* Get a handle to the shortcode service */
        $shortcodeService = $iocContainer->get(org_tubepress_ioc_IocService::SHORTCODE_SERVICE);

        /* Parse each shortcode one at a time */
        while ($shortcodeService->somethingToParse($content, $trigger) && $x < 10) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            $generatedHtml = $shortcodeService->getHtml($content, $iocContainer);
            
            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($tpom->getShortcode(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content); 

            /* replace the shortcode with our new content */
            $currentShortcode = $tpom->getShortcode();
            $content = org_tubepress_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $content);
        }
        return $content;
    }

    public static function headAction()
    {
        print org_tubepress_gallery_TubePressGalleryImpl::printHeadElements(false, $_GET);
    }

    public static function initAction()
    {
        wp_enqueue_script('jquery');
    }
}

?>
