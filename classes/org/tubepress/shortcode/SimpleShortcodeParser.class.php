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
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_ioc_IocContainer',
    'org_tubepress_shortcode_ShortcodeParser'));

/**
 * Parses shortcodes.
 */
class org_tubepress_shortcode_SimpleShortcodeParser implements org_tubepress_shortcode_ShortcodeParser
{
    const LOG_PREFIX = 'Shortcode parser';
    
    /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string                                       $content The haystack in which to search
     * 
     * @return array The associative array of parsed options.
     */
    public function parse($content)
    {
        $ioc  = org_tubepress_ioc_IocContainer::getInstance();
        $tpom = $ioc->get('org_tubepress_options_manager_OptionsManager');
        
        /* what trigger word are we using? */
        $keyword = $tpom->get(org_tubepress_options_category_Advanced::KEYWORD);

        if (!$this->somethingToParse($content, $keyword)) {
            return;
        }

        $toReturn = array();

        /* Match everything in square brackets after the trigger */
        $regexp = "\[$keyword\b(.*)\]";

        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Regular expression for content is <tt>%s</tt>', $regexp);

        preg_match("/$regexp/", $content, $matches);

        if (sizeof($matches) === 0) {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'No shortcodes detected in content');
            return;
        }

        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Found a shortcode: <tt>%s</tt>', $matches[0]);

        $tpom->setShortcode($matches[0]);

        /* Anything matched? */
        if (isset($matches[1]) && $matches[1] != '') {

            $text    = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', $matches[1]);
            $text    = self::_convertQuotes($text);
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s*,)?(?:\s|$)/';

            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {

                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Custom options detected in shortcode: <tt>%s</tt>', $matches[0]);

                $toReturn = self::_parseCustomOption($toReturn, $match, $ioc);

                $tpom->setCustomOptions($toReturn);
            }
        } else {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'No custom options detected in shortcode: <tt>%s</tt>', $matches[0]);
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
     * @param array $customOptions The custom options array
     * @param array $match         The array shortcode matches
     *
     * @return void
     */
    private static function _parseCustomOption($customOptions, $match, org_tubepress_ioc_IocService $ioc)
    {
        $inputValidationService = $ioc->get('org_tubepress_options_validation_InputValidationService');

        foreach ($match as $m) {

            if (!empty($m[1])) {
                $name  = $m[1];
                $value = self::_normalizeValue($m[2]);
            } elseif (!empty($m[3])) {
                $name  = $m[3];
                $value = self::_normalizeValue($m[4]);
            } elseif (!empty($m[5])) {
                $name  = $m[5];
                $value = self::_normalizeValue($m[6]);
            }

            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Custom shortcode detected: <tt>%s = %s</tt>', $name, (string)$value);

            try {
                $inputValidationService->validate($name, $value);
                $customOptions[$name] = $value;
            } catch (Exception $e) {
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Ignoring invalid value for "<tt>%s</tt>" option: <tt>%s</tt>', $name, $e->getMessage());
            }
        }
        return $customOptions;
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
    
    /**
     * Strips out ugly slashes and converts boolean
     *
     * @param string $value The raw option name or value
     * 
     * @return string The cleaned up option name or value
     */
    private static function _normalizeValue($value)
    {
        $cleanValue = trim(stripcslashes($value));
        if ($cleanValue == 'true') {
            return true;
        }
        if ($cleanValue == 'false') {
            return false;
        }
        return $cleanValue;
    }
}

