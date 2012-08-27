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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_GallerySource',
	'org_tubepress_api_const_options_names_Thumbs',
	'org_tubepress_api_const_options_names_InteractiveSearch',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_shortcode_ShortcodeParser',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
));

/**
 * Parses shortcodes.
 */
class org_tubepress_impl_shortcode_SimpleShortcodeParser implements org_tubepress_api_shortcode_ShortcodeParser
{
    private static $_logPrefix = 'Shortcode parser';

    /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string $content The haystack in which to search
     *
     * @return array The associative array of parsed options.
     */
    public function parse($content)
    {
        try {

            $this->_wrappedParse($content);

        } catch (Exception $e) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Caught exception when parsing shortcode: ' . $e->getMessage());
        }
    }

    private function _wrappedParse($content)
    {
        $ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context  = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $keyword  = $context->get(org_tubepress_api_const_options_names_Advanced::KEYWORD);

	    if (!$this->somethingToParse($content, $keyword)) {

            return;
        }

        preg_match("/\[$keyword\b(.*)\]/", $content, $matches);

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Found a shortcode: %s', $matches[0]);

        $context->setActualShortcodeUsed($matches[0]);

        /* Anything matched? */
        if (isset($matches[1]) && $matches[1] != '') {

            $text    = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', utf8_encode($matches[1]));
            $text    = self::_convertQuotes($text);
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s*,)?(?:\s|$)/';

            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {

                org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Candidate options detected in shortcode: %s', $matches[0]);

                $toReturn = self::_buildNameValuePairArray($match, $ioc);

                $context->setCustomOptions($toReturn);
            }

        } else {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'No custom options detected in shortcode: %s', $matches[0]);
        }
    }

    /**
     * Determines if the given content contains a shortcode.
     *
     * @param string $content The content to search through
     * @param string $trigger The shortcode trigger word
     *
     * @return boolean True if there's a shortcode in the content, false otherwise.
     */
    public function somethingToParse($content, $trigger = "tubepress")
    {
        return preg_match("/\[$trigger\b(.*)\]/", $content) === 1;
    }

    /**
     * Handles the detection of a custom options
     *
     * @param array                            $customOptions The custom options array
     * @param array                            $match         The array shortcode matches
     * @param org_tubepress_api_ioc_IocService $ioc           The IOC service
     *
     * @return void
     */
    private static function _buildNameValuePairArray($match, org_tubepress_api_ioc_IocService $ioc)
    {
        $toReturn      = array();
        $pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);

        foreach ($match as $m) {

            if (! empty($m[1])) {

                $name  = $m[1];
                $value = $m[2];

            } elseif (! empty($m[3])) {

                $name  = $m[3];
                $value = $m[4];

            } elseif (! empty($m[5])) {

                $name  = $m[5];
                $value = $m[6];
            }

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Name-value pair detected: %s = "%s" (unfiltered)', $name, $value);

            $filtered = $pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::VARIABLE_READ_FROM_EXTERNAL_INPUT, $value, $name);

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Name-value pair detected: %s = "%s" (filtered)', $name, $filtered);

            $toReturn[$name] = $filtered;
        }

        return $toReturn;
    }

    /**
     * Replaces weird quotes with normal ones. Fun.
     *
     * @param string $text The string to search through
     *
     * @return void
     */
    private static function _convertQuotes($text)
    {
        $converted = str_replace(array('&#8216', '&#8217', '&#8242;'), '\'', $text);
        return str_replace(array('&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $converted);
    }
}
