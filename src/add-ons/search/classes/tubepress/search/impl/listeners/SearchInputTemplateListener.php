<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_search_impl_listeners_SearchInputTemplateListener
{
    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    public function __construct(tubepress_api_options_ContextInterface        $context,
                                tubepress_api_url_UrlFactoryInterface         $urlFactory,
                                tubepress_api_http_RequestParametersInterface $requestParams)
    {
        $this->_context       = $context;
        $this->_urlFactory    = $urlFactory;
        $this->_requestParams = $requestParams;
    }

    public function onSearchInputTemplatePreRender(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var array
         */
        $existingTemplateVars = $event->getSubject();
        $resultsUrl           = $this->_context->get(tubepress_api_options_Names::SEARCH_RESULTS_URL);
        $url                  = '';

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
        $searchTerms = $this->_requestParams->getParamValue('tubepress_search');
        $searchTerms = urldecode($searchTerms);

        /*
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
        $params = $url->getQuery();

        $params->remove('tubepress_page');
        $params->remove('tubepress_search');

        /* apply the template variables */
        $newVars = array(
            tubepress_api_template_VariableNames::SEARCH_HANDLER_URL   => $url->toString(),
            tubepress_api_template_VariableNames::SEARCH_HIDDEN_INPUTS => $params->toArray(),
            tubepress_api_template_VariableNames::SEARCH_TERMS         => $searchTerms,
        );

        $existingTemplateVars = array_merge($existingTemplateVars, $newVars);

        $event->setSubject($existingTemplateVars);
    }
}
