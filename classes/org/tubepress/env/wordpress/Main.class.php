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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_options_storage_WordPressStorageManager',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_shortcode_SimpleShortcodeParser',
    'org_tubepress_ioc_impl_FreeWordPressPluginIocService',
    'org_tubepress_ioc_ProInWordPressIocService',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_ioc_IocContainer',
    'org_tubepress_util_StringUtils',
    'org_tubepress_api_gallery_Gallery',
    'org_tubepress_html_HtmlUtils'));

class org_tubepress_env_wordpress_Main
{
    /**
     * Filters the WordPress content, looking for TubePress shortcodes and replacing them with galleries/videos.
     * 
     * @param string $content The WordPress content.
     *
     * @return string The modified content.
     */
    public static function contentFilter($content = '')
    {
        try {
            /* do as little work as possible here 'cause we might not even run */
            $wpsm    = new org_tubepress_options_storage_WordPressStorageManager();
            $trigger = $wpsm->get(org_tubepress_options_category_Advanced::KEYWORD);
            $parser  = new org_tubepress_shortcode_SimpleShortcodeParser();

            /* no shortcode? get out */
            if (!$parser->somethingToParse($content, $trigger)) {
                return $content;
            }

            return self::_getHtml($content, $trigger, $parser);
        } catch (Exception $e) {
            return $e->getMessage() . $content;
        }
    }

    /**
     * Does the heavy lifting of generating videos/galleries from content.
     * 
     * @param string $content The WordPress content.
     * @param string $trigger The shortcode keyword
     *
     * @return string The modified content.
     */
    private static function _getHtml($content, $trigger, $parser)
    {
        /* Whip up the IOC service */
        $iocContainer = org_tubepress_ioc_IocContainer::getInstance();

        /* Get a handle to our options manager */
        $tpom = $iocContainer->get('org_tubepress_options_manager_OptionsManager');

        /* Turn on logging if we need to */
        org_tubepress_log_Log::setEnabled($tpom->get(org_tubepress_options_category_Advanced::DEBUG_ON), $_GET);

        /* Grab the gallery that will do the heavy lifting */
        $gallery = $iocContainer->get('org_tubepress_api_gallery_Gallery');

        /* Parse each shortcode one at a time */
        while ($parser->somethingToParse($content, $trigger)) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            $generatedHtml = $gallery->getHtml($content);

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($tpom->getShortcode(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $tpom->getShortcode();
            $content          = org_tubepress_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $content);
        }
        return $content;
    }

    /**
     * WordPress head action hook.
     *
     * @return void
     */
    public static function headAction()
    {
        print org_tubepress_html_HtmlUtils::getHeadElementsAsString($_GET, false);
    }

    /**
     * WordPress init action hook.
     *
     * @return void
     */
    public static function initAction()
    {
        wp_enqueue_script('jquery');
    }
}

