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
class tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService implements tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    private $_thumbGalleryShortcodeHandler;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(
        tubepress_api_options_ContextInterface $context,
        tubepress_spi_shortcode_PluggableShortcodeHandlerService $thumbGalleryShortcodeHandler)
    {
        $this->_logger                       = ehough_epilog_LoggerFactory::getLogger('Search Output Shortcode Handler');
        $this->_thumbGalleryShortcodeHandler = $thumbGalleryShortcodeHandler;
        $this->_context                      = $context;
    }

    /**
     * @return string The name of this shortcode handler. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'search-output';
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    public final function shouldExecute()
    {
        $shouldLog   = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /* not configured at all for search results */
        if ($this->_context->get(tubepress_api_const_options_names_Output::OUTPUT) !== tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS) {

            if ($shouldLog) {

                $this->_logger->debug('Not configured for search results');
            }

            return false;
        }

        /* do we have search terms? */
        $qss            = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $rawSearchTerms = $qss->getParamValue(tubepress_spi_const_http_ParamName::SEARCH_TERMS);

        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $this->_context->get(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $hasSearchTerms        = $rawSearchTerms != '';

        /* the user is not searching and we don't have to show results */
        if (! $hasSearchTerms && ! $mustShowSearchResults) {

            if ($shouldLog) {

                $this->_logger->debug('The user isn\'t searching.');
            }

            return false;
        }

        /**
         * At this point we know that the user wants search results, and we don't necessarily have to show results.
         */
        return true;
    }

    /**
     * @return string The HTML for this shortcode handler.
     */
    public final function getHtml()
    {
        $qss            = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $rawSearchTerms = $qss->getParamValue(tubepress_spi_const_http_ParamName::SEARCH_TERMS);
        $hasSearchTerms = $rawSearchTerms != '';
        $shouldLog      = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /* if the user isn't searching, don't display anything */
        if (! $hasSearchTerms) {

            if ($shouldLog) {

                $this->_logger->debug('User doesn\'t appear to be searching. Will not display anything.');
            }

            return '';
        }

        if ($shouldLog) {

            $this->_logger->debug('User is searching. We\'ll handle this.');
        }

        /* who are we searching? */
        switch ($this->_context->get(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)) {

            case 'vimeo':

                $this->_context->set(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_addons_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);

                $this->_context->set(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, $rawSearchTerms);

                break;

            default:

                $this->_context->set(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);

                $this->_context->set(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, $rawSearchTerms);

                break;
        }

        /* display the results as a thumb gallery */
        return $this->_thumbGalleryShortcodeHandler->getHtml();
    }
}
