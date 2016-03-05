<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_wp_ShortcodeListener
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var array
     */
    private $_optionMapCache;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_loggingEnabled;

    public function __construct(tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_api_options_ContextInterface       $context,
                                tubepress_api_html_HtmlGeneratorInterface    $htmlGenerator,
                                tubepress_api_options_ReferenceInterface     $optionsReference,
                                tubepress_api_log_LoggerInterface            $logger)
    {
        $this->_eventDispatcher  = $eventDispatcher;
        $this->_context          = $context;
        $this->_htmlGenerator    = $htmlGenerator;
        $this->_optionsReference = $optionsReference;
        $this->_logger           = $logger;
        $this->_loggingEnabled   = $logger->isEnabled();
    }

    public function onShortcode(tubepress_api_event_EventInterface $incomingEvent)
    {
        $subject                = $incomingEvent->getSubject();
        $rawShortcodeAttributes = $subject[0];
        $rawShortcodeContent    = isset($subject[1]) ? $subject[1] : '';

        if (!is_array($rawShortcodeAttributes)) {

            $rawShortcodeAttributes = array();
        }

        if ($this->_loggingEnabled) {

            $this->_logRawShortcode($rawShortcodeAttributes, $rawShortcodeContent);
        }

        $normalizedOptions = $this->_normalizeIncomingShortcodeOptionMap($rawShortcodeAttributes);

        $this->_context->setEphemeralOptions($normalizedOptions);

        $event = $this->_buildShortcodeEvent($normalizedOptions, $rawShortcodeContent);
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::SHORTCODE_PARSED, $event);

        /* Get the HTML for this particular shortcode. */
        $toReturn = $this->_htmlGenerator->getHtml();

        /* reset the context for the next shortcode */
        $this->_context->setEphemeralOptions(array());

        $incomingEvent->setArgument('result', $toReturn);
    }

    private function _buildShortcodeEvent(array $normalizedOptions, $innerContent)
    {
        if (!$innerContent) {

            $innerContent = null;
        }

        $name      = $this->_context->get(tubepress_api_options_Names::SHORTCODE_KEYWORD);
        $shortcode = new tubepress_shortcode_impl_Shortcode($name, $normalizedOptions, $innerContent);

        return $this->_eventDispatcher->newEventInstance($shortcode);
    }

    private function _normalizeIncomingShortcodeOptionMap(array $optionMap)
    {
        if (!isset($this->_optionMapCache)) {

            $this->_optionMapCache = array();
            $allKnownOptionNames   = $this->_optionsReference->getAllOptionNames();

            foreach ($allKnownOptionNames as $camelCaseOptionName) {

                $asLowerCase                         = strtolower($camelCaseOptionName);
                $this->_optionMapCache[$asLowerCase] = $camelCaseOptionName;
            }
        }

        $toReturn = array();

        foreach ($optionMap as $lowerCaseCandidate => $value) {

            if (isset($this->_optionMapCache[$lowerCaseCandidate])) {

                $camelCaseOptionName            = $this->_optionMapCache[$lowerCaseCandidate];
                $toReturn[$camelCaseOptionName] = $value;
            }
        }

        return $toReturn;
    }

    private function _logRawShortcode(array $rawShortcodeAttributes, $rawShortcodeContent)
    {
        $this->_logDebug(sprintf(

            'WordPress sent us a shortcode to parse with <code>%d</code> attributes.',
            count($rawShortcodeAttributes)
        ));

        if (count($rawShortcodeAttributes) > 0) {

            $this->_logDebug('Attributes follow...');
        }

        foreach ($rawShortcodeAttributes as $key => $value) {

            $printKey   = is_scalar($key) ? (string) $key : json_encode($key);
            $printValue = is_scalar($value) ? (string) $value : json_encode($value);

            $this->_logDebug(sprintf('<code>%s</code> : <code>%s</code>',

                htmlspecialchars($printKey), htmlspecialchars($printValue)
            ));
        }

        $printContent = is_scalar($rawShortcodeContent) ? (string) $rawShortcodeContent : json_encode($rawShortcodeContent);

        $this->_logDebug(sprintf('Shortcode content is: <code>%s</code>', $printContent));
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Shortcode Listener) %s', $msg));
    }
}