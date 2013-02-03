<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Shortcode Parser');
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
        try {

            $this->_wrappedParse($content);

        } catch (Exception $e) {

            $this->_logger->error('Caught exception when parsing shortcode: ' . $e->getMessage());
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

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Found a shortcode: %s', $matches[0]));
        }

        $context->setActualShortcodeUsed($matches[0]);

        /* Anything matched? */
        if (isset($matches[1]) && $matches[1] != '') {

            $text    = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', $matches[1]);
            $text    = self::_convertQuotes($text);
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s*,)?(?:\s|$)/';

            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug(sprintf('Candidate options detected in shortcode: %s', $matches[0]));
                }

                $toReturn = $this->_buildNameValuePairArray($match);

                $context->setCustomOptions($toReturn);
            }

        } else {

            if ($this->_logger->isDebugEnabled()) {

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
        $toReturn      = array();
        $pluginManager = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

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

            if ($this->_logger->isDebugEnabled()) {

                /** @noinspection PhpUndefinedVariableInspection */
                $this->_logger->debug(sprintf('Name-value pair detected: %s = "%s" (unfiltered)', $name, $value));
            }

            /** @noinspection PhpUndefinedVariableInspection */
            $event = new tubepress_api_event_TubePressEvent(

                $value,
                array('optionName' => $name)
            );

            $pluginManager->dispatch(

                tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT,
                $event
            );

            $filtered = $event->getSubject();

            if ($this->_logger->isDebugEnabled()) {

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