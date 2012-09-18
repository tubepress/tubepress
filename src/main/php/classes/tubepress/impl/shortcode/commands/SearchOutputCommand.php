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
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_impl_shortcode_commands_SearchOutputCommand implements ehough_chaingang_api_Command
{
    /**
     * @var ehough_epilog_api_ILogger
     */
    private $_logger;

    /**
     * @var ehough_chaingang_api_Chain
     */
    private $_chain;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Search Output Command');
        $this->_chain  = $chain;
    }

    /**
     * Execute a unit of processing work to be performed.
     *
     * This Command may either complete the required processing and return true,
     * or delegate remaining processing to the next Command in a Chain containing
     * this Command by returning false.
     *
     * @param ehough_chaingang_api_Context $context The Context to be processed by this Command.
     *
     * @return boolean True if the processing of this Context has been completed, or false if the
     *                 processing of this Context should be delegated to a subsequent Command
     *                 in an enclosing Chain.
     */
    public final function execute(ehough_chaingang_api_Context $context)
    {
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        /* not configured at all for search results */
        if ($execContext->get(tubepress_api_const_options_names_Output::OUTPUT) !== tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('Not configured for search results');
            }

            return false;
        }

        /* do we have search terms? */
        $qss            = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $rawSearchTerms = $qss->getParamValue(tubepress_spi_const_http_ParamName::SEARCH_TERMS);

        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $execContext->get(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $hasSearchTerms        = $rawSearchTerms != '';

        /* the user is not searching and we don't have to show results */
        if (! $hasSearchTerms && ! $mustShowSearchResults) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('The user isn\'t searching.');
            }

            return false;
        }

        /* if the user isn't searching, don't display anything */
        if (! $hasSearchTerms) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('User doesn\'t appear to be searching. Will not display anything.');
            }

            $context->put(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML, '');

            return true;
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('User is searching. We\'ll handle this.');
        }

        /* who are we searching? */
        switch ($execContext->get(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)) {

            case tubepress_spi_provider_Provider::VIMEO:

                $execContext->set(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);

                $result = $execContext->set(tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, $rawSearchTerms);

                if ($result !== true) {

                    if ($this->_logger->isDebugEnabled()) {

                        $this->_logger->debug('Unable to set search terms, so we will not handle request');
                    }

                    return false;
                }

                break;

            default:

                $execContext->set(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);

                $result = $execContext->set(tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, $rawSearchTerms);

                if ($result !== true) {

                    if ($this->_logger->isDebugEnabled()) {

                        $this->_logger->debug('Unable to set search terms, so we will not handle request');
                    }

                    return false;
                }

                break;
        }

        /* display the results as a thumb gallery */
        return $this->_chain->execute($context);
    }
}
