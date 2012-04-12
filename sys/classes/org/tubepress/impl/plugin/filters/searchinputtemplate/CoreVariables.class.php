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
    'org_tubepress_api_const_options_names_InteractiveSearch',
    'org_tubepress_api_const_querystring_QueryParamName',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_http_HttpRequestParameterService',
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
        $context    = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $qss        = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $hrps       = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $ms         = $ioc->get(org_tubepress_api_message_MessageService::_);
        $resultsUrl = $context->get(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $url        = '';

        try {

            $url = new org_tubepress_api_url_Url($resultsUrl);

        } catch (Exception $e) {

            org_tubepress_impl_log_Log::log('Search Input Core Filter', $e->getMessage());
        }

        /* if the user didn't request a certain page, just send the search results right back here */
        if ($url == '') {

            $url = new org_tubepress_api_url_Url($qss->getFullUrl($_SERVER));
        }

        /* clean up the search terms a bit */
        $searchTerms = $hrps->getParamValue(org_tubepress_api_const_http_ParamName::SEARCH_TERMS);
        $searchTerms = urldecode($searchTerms);    //TODO: get rid of this once we move to POST?

        /*
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
        $params = $url->getQueryVariables();

        unset($params[org_tubepress_api_const_http_ParamName::PAGE]);
        unset($params[org_tubepress_api_const_http_ParamName::SEARCH_TERMS]);

        /* apply the template variables */
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, $url->toString());
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, $params);
        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_TERMS, $searchTerms);

        $template->setVariable(org_tubepress_api_const_template_Variable::SEARCH_BUTTON, $ms->_('Search'));    //>(translatable)<
        return $template;
    }
}