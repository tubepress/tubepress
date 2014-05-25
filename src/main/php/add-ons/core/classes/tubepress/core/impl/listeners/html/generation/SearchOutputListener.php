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
class tubepress_core_impl_listeners_html_generation_SearchOutputListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_impl_listeners_html_generation_ThumbGalleryListener
     */
    private $_thumbGalleryShortcodeHandler;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_requestParams;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_core_api_options_ContextInterface        $context,
                                tubepress_core_impl_listeners_html_generation_ThumbGalleryListener      $thumbGalleryShortcodeHandler,
                                tubepress_core_api_http_RequestParametersInterface $requestParams)
    {
        $this->_logger                       = $logger;
        $this->_thumbGalleryShortcodeHandler = $thumbGalleryShortcodeHandler;
        $this->_context                      = $context;
        $this->_requestParams                = $requestParams;
    }

    public function onHtmlGeneration(tubepress_core_api_event_EventInterface $event)
    {
        $shouldLog   = $this->_logger->isEnabled();

        /* not configured at all for search results */
        if ($this->_context->get(tubepress_core_api_const_options_Names::OUTPUT) !== tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_RESULTS) {

            if ($shouldLog) {

                $this->_logger->debug('Not configured for search results');
            }

            return;
        }

        /* do we have search terms? */
        $rawSearchTerms = $this->_requestParams->getParamValue(tubepress_core_api_const_http_ParamName::SEARCH_TERMS);

        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $this->_context->get(tubepress_core_api_const_options_Names::SEARCH_RESULTS_ONLY);
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

    private function _handle(tubepress_core_api_event_EventInterface $event)
    {
        $rawSearchTerms = $this->_requestParams->getParamValue(tubepress_core_api_const_http_ParamName::SEARCH_TERMS);
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

        /* who are we searching? */
        switch ($this->_context->get(tubepress_core_api_const_options_Names::SEARCH_PROVIDER)) {

            case 'vimeo':

                $this->_context->set(tubepress_core_api_const_options_Names::GALLERY_SOURCE, tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH);

                $this->_context->set(tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE, $rawSearchTerms);

                break;

            default:

                $this->_context->set(tubepress_core_api_const_options_Names::GALLERY_SOURCE, tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH);

                $this->_context->set(tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE, $rawSearchTerms);

                break;
        }

        /* display the results as a thumb gallery */
        $this->_thumbGalleryShortcodeHandler->onHtmlGeneration($event);
    }
}
