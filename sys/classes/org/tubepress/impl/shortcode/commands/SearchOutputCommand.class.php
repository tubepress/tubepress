<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_patterns_cor_Command',
));

/**
 * HTML generation command that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_shortcode_commands_SearchOutputCommand implements org_tubepress_api_patterns_cor_Command
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
        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');    
        
        /* not configured at all for search results */
        if ($execContext->get(org_tubepress_api_const_options_names_Output::OUTPUT) !== org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Not configured for search results');
            return false;
        }

        /* do we have search terms? */
        $qss            = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $rawSearchTerms = $qss->getSearchTerms($_GET);
        
        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $execContext->get(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY);
        $hasSearchTerms        = $rawSearchTerms != '';

        /* the user is not searching and we don't have to show results */
        if(!$hasSearchTerms && ! $mustShowSearchResults) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'The user isn\'t searching.');
            return false;
        }

        /* if the user isn't searching, don't display anything */
        if (!$hasSearchTerms) {
            
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'User doesn\'t appear to be searching. Will not display anything.');
            $html = '';
            return true;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'User is searching. We\'ll handle this.');
        
        /* clean up the search terms */
        $searchTerms = org_tubepress_impl_util_StringUtils::cleanForSearch($rawSearchTerms);
        
        /* who are we searching? */
        switch ($execContext->get(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER)) {
            
            case 'vimeo':
                $execContext->set(org_tubepress_api_const_options_names_Output::MODE, org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH);
                $execContext->set(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE, $searchTerms);
                break;
                
            default:
                $execContext->set(org_tubepress_api_const_options_names_Output::MODE, org_tubepress_api_const_options_values_ModeValue::TAG);
                $execContext->set(org_tubepress_api_const_options_names_Output::TAG_VALUE, $searchTerms);
                break;
        }
        
        /* display the results as a thumb gallery */
        $html = $ioc->get('org_tubepress_api_patterns_cor_Chain')->execute($context, array(
            'org_tubepress_impl_shortcode_commands_ThumbGalleryCommand'
        ));
        
        return true;
    }

}
