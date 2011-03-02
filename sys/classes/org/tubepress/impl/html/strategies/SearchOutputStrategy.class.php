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

tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_api_const_options_names_Output'));

/**
 * HTML generation strategy that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_html_strategies_SearchOutputStrategy implements org_tubepress_api_patterns_Strategy
{
    const LOG_PREFIX = 'Search Output Strategy';

    private $_ioc;
    private $_tpom;
    private $_rawSearchTerms;

    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    public function start()
    {
        $this->_ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpom = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
    }

    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    public function stop()
    {
        unset($this->_ioc);
        unset($this->_tpom);
        unset($this->_rawSearchTerms);
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    public function canHandle()
    {
        /* not configured at all for search results */
        if ($this->_tpom->get(org_tubepress_api_const_options_names_Output::OUTPUT) !== org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS) {
            return false;
        }

        /* do we have search terms? */
        $qss                   = $this->_ioc->get('org_tubepress_api_querystring_QueryStringService');
        $this->_rawSearchTerms = $qss->getSearchTerms($_GET);
        
        /* are we set up for a gallery fallback? */
        $mustShowSearchResults = $this->_tpom->get(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY);

        /* either the user is searching, or they've requested that we only show search results */
        return $this->_rawSearchTerms != '' || $mustShowSearchResults;
    }

    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    public function execute()
    {
        /* if the user isn't searching, don't display anything */
        if ($this->_rawSearchTerms == '') {
            
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'User doesn\'t appear to be searching. Will not display anything.');
            return '';
        }

        /* clean up the search terms */
        $searchTerms = org_tubepress_impl_util_StringUtils::cleanForSearch($this->_rawSearchTerms);
        
        /* who are we searching? */
        switch ($this->_tpom->get(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER)) {
            
            case 'vimeo':
                $this->_tpom->set(org_tubepress_api_const_options_names_Output::MODE, org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH);
                $this->_tpom->set(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE, $searchTerms);
                break;
                
            default:
                $this->_tpom->set(org_tubepress_api_const_options_names_Output::MODE, org_tubepress_api_const_options_values_ModeValue::TAG);
                $this->_tpom->set(org_tubepress_api_const_options_names_Output::TAG_VALUE, $searchTerms);
                break;
        }
        
        /* display the results as a thumb gallery */
        //TODO: what if this bails?
        return $this->_ioc->get('org_tubepress_api_patterns_StrategyManager')->executeStrategy(array(
            'org_tubepress_impl_html_strategies_ThumbGalleryStrategy'
        ));
    }

}
