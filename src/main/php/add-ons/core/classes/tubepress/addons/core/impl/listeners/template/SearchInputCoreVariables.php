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
 * Adds some core variables to the search input template.
 */
class tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables
{
    /**
     * @var tubepress_api_url_CurrentUrlServiceInterface
     */
    private $_currentUrlService;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_api_url_CurrentUrlServiceInterface $currentUrlService,
        tubepress_api_translation_TranslatorInterface $translator,
        tubepress_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_currentUrlService = $currentUrlService;
        $this->_translator        = $translator;
        $this->_urlFactory        = $urlFactory;
    }

    public function onSearchInputTemplate(tubepress_api_event_EventInterface $event)
    {
        $template   = $event->getSubject();
        $context    = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $hrps       = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $resultsUrl = $context->get(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $url        = '';

        try {

            $url = $this->_urlFactory->fromString($resultsUrl);

        } catch (Exception $e) {

            //this is not a real problem, as the user might simply not supply a custom URL
        }

        /* if the user didn't request a certain page, just send the search results right back here */
        if ($url == '') {

            $url = $this->_currentUrlService->getUrl();
        }

        /* clean up the search terms a bit */
        $searchTerms = $hrps->getParamValue(tubepress_spi_const_http_ParamName::SEARCH_TERMS);
        $searchTerms = urldecode($searchTerms);    //TODO: get rid of this once we move to POST?

        /*
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
        $params = $url->getQuery();

        $params->remove(tubepress_spi_const_http_ParamName::PAGE);
        $params->remove(tubepress_spi_const_http_ParamName::SEARCH_TERMS);

        /* apply the template variables */
        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, $url->toString());
        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, $params->toArray());
        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_TERMS, $searchTerms);

        $template->setVariable(tubepress_api_const_template_Variable::SEARCH_BUTTON, $this->_translator->_('Search'));    //>(translatable)<
    }
}