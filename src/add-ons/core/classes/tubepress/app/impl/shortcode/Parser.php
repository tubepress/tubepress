<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_app_impl_shortcode_Parser implements tubepress_app_api_shortcode_ParserInterface
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_platform_api_util_StringUtilsInterface
     */
    private $_stringUtils;
    
    /**
     * @var string
     */
    private $_lastShortcodeUsed = null;

    public function __construct(tubepress_platform_api_log_LoggerInterface       $logger,
                                tubepress_app_api_options_ContextInterface       $context,
                                tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_platform_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_logger          = $logger;
        $this->_shouldLog       = $logger->isEnabled();
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_stringUtils     = $stringUtils;
    }

    /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string $content The haystack in which to search
     *
     * @return array The associative array of parsed options.
     *
     * @deprecated
     */
    public function parse($content)
    {
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
        $keyword = $this->_context->get(tubepress_app_api_options_Names::SHORTCODE_KEYWORD);

        if (!$this->somethingToParse($content, $keyword)) {

            return;
        }

        preg_match("/\[$keyword\b(.*)\]/", $content, $matches);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found a shortcode: %s', $this->_stringUtils->redactSecrets($matches[0])));
        }

        $this->_lastShortcodeUsed = $matches[0];

        /* Anything matched? */
        if (isset($matches[1]) && $matches[1] != '') {

            $text    = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', $matches[1]);
            $text    = self::_convertQuotes($text);
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s*,)?(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s*,)?(?:\s|$)/';

            if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Candidate options detected in shortcode: %s', $this->_stringUtils->redactSecrets($matches[0])));
                }

                $toReturn = $this->_buildNameValuePairArray($match);

                $this->_context->setEphemeralOptions($toReturn);
            }

        } else {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('No custom options detected in shortcode: %s', $this->_stringUtils->redactSecrets($matches[0])));
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
     *
     * @deprecated
     */
    public function somethingToParse($content, $trigger = "tubepress")
    {
        return preg_match("/\[$trigger\b(.*)\]/", $content) === 1;
    }

    /**
     * @return string|null The last shortcode used, or null if never parsed.
     *
     * @deprecated
     */
    public function getLastShortcodeUsed()
    {
        return $this->_lastShortcodeUsed;
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

                $this->_logger->debug(sprintf('Name-value pair detected: %s = "%s" (unfiltered)', $name, $this->_stringUtils->redactSecrets($value)));
            }

            $event = $this->_eventDispatcher->newEventInstance(

                $value,
                array('optionName' => $name)
            );

            $this->_eventDispatcher->dispatch(

                tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT,
                $event
            );

            $filtered = $event->getSubject();

            $event = $this->_eventDispatcher->newEventInstance($filtered, array(
                'optionName' => $name
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT . ".$name",
                $event
            );

            $filtered = $event->getSubject();

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Name-value pair detected: %s = "%s" (filtered)', $name, $this->_stringUtils->redactSecrets($filtered)));
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
        $converted = str_replace(array('&#8216;', '&#8217;', '&#8242;'), '\'', $text);

        return str_replace(array('&#34;', '&#8220;', '&#8221;', '&#8243;'), '"', $converted);
    }
}