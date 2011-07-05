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
    'org_tubepress_api_const_querystring_QueryParamName',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_api_template_Template',
    'org_tubepress_api_url_Url',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_util_StringUtils',
));

/**
 * Adds some core variables to the search input template.
 */
class org_tubepress_impl_plugin_filters_searchinputtemplate_CoreVariables
{
    public function alter_searchInputTemplate(org_tubepress_api_template_Template $template)
    {
        $ioc        = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context    = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $qss        = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $ms         = $ioc->get('org_tubepress_api_message_MessageService');
        $resultsUrl = $context->get(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL);
        
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
        
        return $template;
    }
}