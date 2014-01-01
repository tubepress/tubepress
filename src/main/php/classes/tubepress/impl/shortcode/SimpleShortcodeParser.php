<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Parses shortcodes.
 */
class tubepress_impl_shortcode_SimpleShortcodeParser implements tubepress_spi_shortcode_ShortcodeParser
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Shortcode Parser');
    }

    /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string $content The haystack in which to search
     *
     * @return array The associative array of parsed options.
     */
    public function parse($content)
    {
        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        try {

            $this->_wrappedParse($content);

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error('Caught exception when parsing shortcode: ' . $e->getMessage());
            }
        }
    }

    private function _wrappedParse($content)
    {
        $context  = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $keyword  = $context->get(tubepress_api_const_options_names_Advanced::KEYWORD);

        if (!$this->somethingToParse($content, $keyword)) {

            return;
        }

        preg_match("/\[$keyword\b(.*)\]/", $content, $matches);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found a shortcode: %s', $matches[0]));
        }

        $context->setActualShortcodeUsed($matches[0]);

        /* Anything matched? */
        if (isset($matches[1]) && $matches[1] != '') {

            $text    = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', $matches[1]);
            $text    = self::_convertQuotes($text);
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s*,)?(?:\s|$)/';

            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Candidate options detected in shortcode: %s', $matches[0]));
                }

                $toReturn = $this->_buildNameValuePairArray($match);

                $context->setCustomOptions($toReturn);
            }

        } else {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('No custom options detected in shortcode: %s', $matches[0]));
            }
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
     * @param array                            $match         The array shortcode matches
     *
     * @return array The name value pair array.
     */
    private function _buildNameValuePairArray($match)
    {
        $toReturn        = array();
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $value           = null;

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

            if (! isset($name) || ! isset($value)) {

                continue;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Name-value pair detected: %s = "%s" (unfiltered)', $name, $value));
            }

            $event = new tubepress_spi_event_EventBase(

                $value,
                array('optionName' => $name)
            );

            $eventDispatcher->dispatch(

                tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL,
                $event
            );

            $filtered = $event->getSubject();

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Name-value pair detected: %s = "%s" (filtered)', $name, $filtered));
            }

            $toReturn[$name] = $filtered;
        }

        return $toReturn;
    }

    /**
     * Replaces weird quotes with normal ones. Fun.
     *
     * @param string $text The string to search through
     *
     * @return string The converted string.
     */
    private static function _convertQuotes($text)
    {
        $converted = str_replace(array('&#8216', '&#8217', '&#8242;'), '\'', $text);

        return str_replace(array('&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $converted);
    }
}