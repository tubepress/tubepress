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

org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_spi_patterns_cor_Command'
));

/**
 * HTML generation command that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_shortcode_commands_SearchOutputCommand implements org_tubepress_spi_patterns_cor_Command
{
    const LOG_PREFIX = 'Search Output Command';

    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        /* not configured at all for search results */
        if ($execContext->get(org_tubepress_api_const_options_names_Output::OUTPUT) !== org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Not configured for search results');
            return false;
        }

        /* do we have search terms? */
        $qss            = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $rawSearchTerms = $qss->getParamValue(org_tubepress_api_const_http_ParamName::SEARCH_TERMS);

        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $execContext->get(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $hasSearchTerms        = $rawSearchTerms != '';

        /* the user is not searching and we don't have to show results */
        if (!$hasSearchTerms && ! $mustShowSearchResults) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'The user isn\'t searching.');
            return false;
        }

        /* if the user isn't searching, don't display anything */
        if (!$hasSearchTerms) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'User doesn\'t appear to be searching. Will not display anything.');
            $context->returnValue = '';
            return true;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'User is searching. We\'ll handle this.');

        /* who are we searching? */
        switch ($execContext->get(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)) {

        case org_tubepress_api_provider_Provider::VIMEO:

            $execContext->set(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE, org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);

            $result = $execContext->set(org_tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, $rawSearchTerms);

            if ($result !== true) {

                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Unable to set search terms, so we will not handle request');
                return false;
            }

            break;

        default:

            $execContext->set(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE, org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);

            $result = $execContext->set(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, $rawSearchTerms);

            if ($result !== true) {

                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Unable to set search terms, so we will not handle request');
                return false;
            }

            break;
        }

        /* display the results as a thumb gallery */
        return $ioc->get(org_tubepress_spi_patterns_cor_Chain::_)->execute($context, array(
            'org_tubepress_impl_shortcode_commands_ThumbGalleryCommand'
        ));
    }
}
