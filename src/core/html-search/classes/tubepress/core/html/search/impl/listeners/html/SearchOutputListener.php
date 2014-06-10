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
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_core_html_search_impl_listeners_html_SearchOutputListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_http_api_RequestParametersInterface $requestParams)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_requestParams = $requestParams;
    }

    public function onHtmlGeneration(tubepress_core_event_api_EventInterface $event)
    {
        $shouldLog   = $this->_logger->isEnabled();

        /* not configured at all for search results */
        if ($this->_context->get(tubepress_core_html_api_Constants::OPTION_OUTPUT) !== tubepress_core_html_search_api_Constants::OUTPUT_SEARCH_RESULTS) {

            if ($shouldLog) {

                $this->_logger->debug('Not configured for search results');
            }

            return;
        }

        /* do we have search terms? */
        $rawSearchTerms = $this->_requestParams->getParamValue(tubepress_core_html_search_api_Constants::HTTP_PARAM_NAME_SEARCH_TERMS);

        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $this->_context->get(tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY);
        $hasSearchTerms        = $rawSearchTerms != '';

        /* the user is not searching and we don't have to show results */
        if (! $hasSearchTerms && ! $mustShowSearchResults) {

            if ($shouldLog) {

                $this->_logger->debug('The user isn\'t searching.');
            }

            return;
        }

        $this->_handle($event);
    }

    public function setMediaProviders(array $mediaProviders)
    {
        $this->_mediaProviders = $mediaProviders;
    }

    private function _handle(tubepress_core_event_api_EventInterface $event)
    {
        $rawSearchTerms = $this->_requestParams->getParamValue(tubepress_core_html_search_api_Constants::HTTP_PARAM_NAME_SEARCH_TERMS);
        $hasSearchTerms = $rawSearchTerms != '';
        $shouldLog      = $this->_logger->isEnabled();

        /* if the user isn't searching, don't display anything */
        if (! $hasSearchTerms) {

            if ($shouldLog) {

                $this->_logger->debug('User doesn\'t appear to be searching. Will not display anything.');
            }

            $event->setSubject('');
            $event->stopPropagation();
            return;
        }

        if ($shouldLog) {

            $this->_logger->debug('User is searching. We\'ll handle this.');
        }

        $provider  = $this->_findMediaProvider();
        $modeName  = $provider->getSearchModeName();
        $valueName = $provider->getSearchQueryOptionName();

        $this->_context->setEphemeralOption(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE, $modeName);
        $this->_context->setEphemeralOption($valueName, $rawSearchTerms);
    }

    private function _findMediaProvider()
    {
        $name = $this->_context->get(tubepress_core_html_search_api_Constants::OPTION_SEARCH_PROVIDER);

        foreach ($this->_mediaProviders as $mediaProvider) {

            if ($mediaProvider->getName() === $name) {

                return $mediaProvider;
            }
        }

        return null;
    }
}
