<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_values_GallerySourceValue',
    'org_tubepress_api_html_HeadHtmlGenerator',
    'org_tubepress_api_shortcode_ShortcodeHtmlGenerator',
    'org_tubepress_api_shortcode_ShortcodeParser',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_options_WordPressStorageManager',
    'org_tubepress_impl_util_StringUtils',
));

class org_tubepress_impl_env_wordpress_Main
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
        /* Whip up the IOC service */
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        /* do as little work as possible here 'cause we might not even run */
        $wpsm    = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $trigger = $wpsm->get(org_tubepress_api_const_options_names_Advanced::KEYWORD);
        $parser  = $ioc->get(org_tubepress_api_shortcode_ShortcodeParser::_);

        /* no shortcode? get out */
        if (!$parser->somethingToParse($content, $trigger)) {
            return $content;
        }

        return self::_getHtml($content, $trigger, $parser, $ioc);
    }

    /**
     * Does the heavy lifting of generating videos/galleries from content.
     *
     * @param string $content The WordPress content.
     * @param string $trigger The shortcode keyword
     *
     * @return string The modified content.
     */
    private static function _getHtml($content, $trigger, $parser, $ioc)
    {
        $ms      = $ioc->get(org_tubepress_api_message_MessageService::_);
        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $gallery = $ioc->get(org_tubepress_api_shortcode_ShortcodeHtmlGenerator::_);

        /* Parse each shortcode one at a time */
        while ($parser->somethingToParse($content, $trigger)) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            try {

                $generatedHtml = $gallery->getHtmlForShortcode($content);

            } catch (Exception $e) {

                $generatedHtml = $e->getMessage();
            }

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($context->getActualShortcodeUsed(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $context->getActualShortcodeUsed();
            $content          = org_tubepress_impl_util_StringUtils::replaceFirst($currentShortcode, $generatedHtml, $content);

            /* reset the context for the next shortcode */
            $context->reset();
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
        /* no need to print anything in the head of the admin section */
        if (is_admin()) {
            return;
        }

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $hh  = $ioc->get(org_tubepress_api_html_HeadHtmlGenerator::_);

        /* this inline JS helps initialize TubePress */
        $inlineJs = $hh->getHeadInlineJs();

        /* this meta stuff prevents search engines from indexing gallery pages > 1 */
        $meta = $hh->getHeadHtmlMeta();

        print <<<EOT
$inlineJs
$meta
EOT;
    }

    /**
     * WordPress init action hook.
     *
     * @return void
     */
    public static function initAction()
    {
        /* no need to queue any of this stuff up in the admin section or login page */
        if (is_admin() || __FILE__ === 'wp-login.php') {
            return;
        }

	$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fse      = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $baseName = $fse->getTubePressInstallationDirectoryBaseName();

        wp_register_script('tubepress', plugins_url("$baseName/sys/ui/static/js/tubepress.js", $baseName));
        wp_register_style('tubepress', plugins_url("$baseName/sys/ui/themes/default/style.css", $baseName));

        wp_enqueue_script('jquery');
        wp_enqueue_script('tubepress');

        wp_enqueue_style('tubepress');
    }
}

