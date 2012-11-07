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
 * Adds some core variables to the search input template.
 */
class tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables
{
    public function onSearchInputTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $template   = $event->getSubject();
        $context    = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $qss        = tubepress_impl_patterns_ioc_KernelServiceLocator::getQueryStringService();
        $hrps       = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $ms         = tubepress_impl_patterns_ioc_KernelServiceLocator::getMessageService();
        $resultsUrl = $context->get(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $url        = '';

        try {

            $url = new ehough_curly_Url($resultsUrl);

        } catch (Exception $e) {

            //this is not a real problem, as the user might simply not supply a custom URL
        }

        /* if the user didn't request a certain page, just send the search results right back here */
        if ($url == '') {

            $url = new ehough_curly_Url($qss->getFullUrl($_SERVER));
        }

        /* clean up the search terms a bit */
        $searchTerms = $hrps->getParamValue(tubepress_spi_const_http_ParamName::SEARCH_TERMS);
        $searchTerms = urldecode($searchTerms);    //TODO: get rid of this once we move to POST?

        /*
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
        $params = $url->getQueryVariables();

        unset($params[tubepress_spi_const_http_ParamName::PAGE]);
        unset($params[tubepress_spi_const_http_ParamName::SEARCH_TERMS]);

        /* apply the template variables */
        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, $url->toString());
        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, $params);
        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_TERMS, $searchTerms);

        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_BUTTON, $ms->_('Search'));    //>(translatable)<
    }
}