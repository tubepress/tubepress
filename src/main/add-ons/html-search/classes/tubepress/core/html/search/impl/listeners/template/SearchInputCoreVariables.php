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
class tubepress_core_html_search_impl_listeners_template_SearchInputCoreVariables
{
    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_url_api_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParamsInterface;

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_translation_api_TranslatorInterface $translator,
                                tubepress_core_url_api_UrlFactoryInterface         $urlFactory,
                                tubepress_core_http_api_RequestParametersInterface $requestParams)
    {
        $this->_context                = $context;
        $this->_translator             = $translator;
        $this->_urlFactory             = $urlFactory;
        $this->_requestParamsInterface = $requestParams;
    }

    public function onSearchInputTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $template   = $event->getSubject();
        $resultsUrl = $this->_context->get(tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_URL);
        $url        = '';

        try {

            $url = $this->_urlFactory->fromString($resultsUrl);

        } catch (Exception $e) {

            //this is not a real problem, as the user might simply not supply a custom URL
        }

        /* if the user didn't request a certain page, just send the search results right back here */
        if ($url == '') {

            $url = $this->_urlFactory->fromCurrent();
        }

        /* clean up the search terms a bit */
        $searchTerms = $this->_requestParamsInterface->getParamValue(tubepress_core_http_api_Constants::PARAM_NAME_SEARCH_TERMS);
        $searchTerms = urldecode($searchTerms);    //TODO: get rid of this once we move to POST?

        /*
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
        $params = $url->getQuery();

        $params->remove(tubepress_core_http_api_Constants::PARAM_NAME_PAGE);
        $params->remove(tubepress_core_http_api_Constants::PARAM_NAME_SEARCH_TERMS);

        /* apply the template variables */
        $template->setVariable(tubepress_core_template_api_const_VariableNames::SEARCH_HANDLER_URL, $url->toString());
        $template->setVariable(tubepress_core_template_api_const_VariableNames::SEARCH_HIDDEN_INPUTS, $params->toArray());
        $template->setVariable(tubepress_core_template_api_const_VariableNames::SEARCH_TERMS, $searchTerms);

        $template->setVariable(tubepress_core_template_api_const_VariableNames::SEARCH_BUTTON, $this->_translator->_('Search'));    //>(translatable)<
    }
}