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

         /* Make a copy of the content that we'll edit */
        $newcontent = $content;

        /* Parse each shortcode one at a time */
        while ($shortcodeService->somethingToParse($newcontent, $trigger)) {

            $shortcodeService->parse($newcontent, $tpom);
            $currentShortcode = $tpom->getShortcode();

            if ($tpom->get(org_tubepress_options_category_Gallery::VIDEO) != '') {
                $videoId = $tpom->get(org_tubepress_options_category_Gallery::VIDEO);
                $log->log('WordPress Main', 'Building single video with ID %s', $videoId);
                if (!isset($singleVideoGenerator)) {
                    $singleVideoGenerator = $iocContainer->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);
                }
                $generatedHtml = $singleVideoGenerator->getSingleVideoHtml($videoId);
            } else {
                $rand = mt_rand();
                $log->log('WordPress Main', 'Starting to build gallery %s', $rand);

                if (!isset($gallery)) {
                    $gallery = $iocContainer->get(org_tubepress_ioc_IocService::GALLERY);
                }

                $generatedHtml = $gallery->getHtml($rand);
            }

            /* remove any leading/trailing <p> tags from the shortcode */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($currentShortcode, '/') . ')(\s*<\/[P|p]>)/';
            $newcontent = preg_replace($pattern, '${2}', $newcontent); 

            /* replace the shortcode with our new content */
            $newcontent = org_tubepress_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $newcontent);
        }
        return $newcontent;
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
