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
        $context  = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
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

            $text    = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', utf8_encode($matches[1]));
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
        $pluginManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

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
