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
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_values_OutputValue',
    'org_tubepress_api_url_Url'));

/**
 * HTML generation strategy that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_html_strategies_SearchInputStrategy implements org_tubepress_api_patterns_Strategy
{
    const LOG_PREFIX = 'Search Input Strategy';

    private $_ioc;
    protected $_tpom;

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
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    public function canHandle()
    {
        return $this->_tpom->get(org_tubepress_api_const_options_names_Output::OUTPUT) === org_tubepress_api_const_options_values_OutputValue::SEARCH_INPUT;
    }

    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    public function execute()
    {
        $th         = $this->_ioc->get('org_tubepress_api_theme_ThemeHandler');
        $ms         = $this->_ioc->get('org_tubepress_api_message_MessageService');
        $template   = $th->getTemplateInstance($this->getTemplatePath());
        $resultsUrl = $this->_tpom->get(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL);
        $qss        = $this->_ioc->get('org_tubepress_api_querystring_QueryStringService');
        
        /* if the user didn't request a certain page, just send the search results right back here */
        if ($resultsUrl == '') {
            $resultsUrl = $qss->getFullUrl($_SERVER);
        }
        
        /* clean up the search terms a bit */
        $searchTerms = urldecode($qss->getSearchTerms($_GET));
        $searchTerms = org_tubepress_impl_util_StringUtils::cleanForSearch($searchTerms);
        
        /* 
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
        $url    = new org_tubepress_api_url_Url($resultsUrl);
        $params = $url->getQueryVariables();
        unset($params[org_tubepress_api_const_querystring_QueryParamName::PAGE]);
        unset($params[org_tubepress_api_const_querystring_QueryParamName::SEARCH_TERMS]);

        /* apply the template variables */
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, $resultsUrl);
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, $params);
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_TERMS, $searchTerms);
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_BUTTON, $ms->_('search-input-button'));
        
        $this->applyTemplateVariables($template);
        
        return $template->toString();
    }
    
    protected function applyTemplateVariables($template)
    {
        //override point
    }
    
    protected function getTemplatePath()
    {
        return 'search/search_input.tpl.php';
    }

}
