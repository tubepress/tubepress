<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_search_impl_listeners_SearchListener
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders = array();

    public function __construct(tubepress_api_log_LoggerInterface             $logger,
                                tubepress_api_options_ContextInterface        $context,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_http_RequestParametersInterface $requestParams)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_templating    = $templating;
        $this->_requestParams = $requestParams;
    }

    public function onHtmlGenerationSearchInput(tubepress_api_event_EventInterface $event)
    {
        if ($this->_context->get(tubepress_api_options_Names::HTML_OUTPUT) !== tubepress_api_options_AcceptableValues::OUTPUT_SEARCH_INPUT) {

            return;
        }

        $html = $this->_templating->renderTemplate('search/input', array());

        $event->setSubject($html);
        $event->stopPropagation();
    }

    public function onHtmlGenerationSearchOutput(tubepress_api_event_EventInterface $event)
    {
        $shouldLog = $this->_logger->isEnabled();

        /* not configured at all for search results */
        if ($this->_context->get(tubepress_api_options_Names::HTML_OUTPUT) !== tubepress_api_options_AcceptableValues::OUTPUT_SEARCH_RESULTS) {

            if ($shouldLog) {

                $this->_logDebug('Not configured for search results');
            }

            return;
        }

        /* do we have search terms? */
        $rawSearchTerms = $this->_requestParams->getParamValue('tubepress_search');

        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $this->_context->get(tubepress_api_options_Names::SEARCH_RESULTS_ONLY);
        $hasSearchTerms        = $rawSearchTerms != '';

        /* the user is not searching and we don't have to show results */
        if (!$hasSearchTerms && !$mustShowSearchResults) {

            if ($shouldLog) {

                $this->_logDebug('The user isn\'t searching.');
            }

            return;
        }

        $this->_handleSearchOutput($event);
    }

    public function onAcceptableValues(tubepress_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $toAdd = array();

        /*
         * @var tubepress_spi_media_MediaProviderInterface
         */
        foreach ($this->_mediaProviders as $mediaProvider) {

            $toAdd[$mediaProvider->getName()] = $mediaProvider->getDisplayName();
        }

        $event->setSubject(array_merge($current, $toAdd));
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _handleSearchOutput(tubepress_api_event_EventInterface $event)
    {
        $rawSearchTerms = $this->_requestParams->getParamValue('tubepress_search');
        $hasSearchTerms = $rawSearchTerms != '';
        $shouldLog      = $this->_logger->isEnabled();

        /* if the user isn't searching, don't display anything */
        if (!$hasSearchTerms) {

            if ($shouldLog) {

                $this->_logDebug('User doesn\'t appear to be searching. Will not display anything.');
            }

            $event->setSubject('');
            $event->stopPropagation();

            return;
        }

        if ($shouldLog) {

            $this->_logDebug('User is searching. We\'ll handle this.');
        }

        $provider  = $this->_findMediaProvider();
        $modeName  = $provider->getSearchModeName();
        $valueName = $provider->getSearchQueryOptionName();

        $this->_context->setEphemeralOption(tubepress_api_options_Names::GALLERY_SOURCE, $modeName);
        $this->_context->setEphemeralOption($valueName, $rawSearchTerms);
    }

    private function _findMediaProvider()
    {
        $name = $this->_context->get(tubepress_api_options_Names::SEARCH_PROVIDER);

        foreach ($this->_mediaProviders as $mediaProvider) {

            if ($mediaProvider->getName() === $name) {

                return $mediaProvider;
            }
        }

        return null;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Search Listener) %s', $msg));
    }
}
