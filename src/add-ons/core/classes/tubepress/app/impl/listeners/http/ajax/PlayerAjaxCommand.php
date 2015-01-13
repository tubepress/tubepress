<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_app_impl_listeners_http_ajax_PlayerAjaxCommand
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_api_media_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_lib_api_http_ResponseCodeInterface
     */
    private $_responseCode;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_app_api_options_ContextInterface        $context,
                                tubepress_app_api_media_CollectorInterface        $collector,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_http_ResponseCodeInterface      $responseCode,
                                tubepress_lib_api_template_TemplatingInterface    $templating)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_collector     = $collector;
        $this->_requestParams = $requestParams;
        $this->_responseCode  = $responseCode;
        $this->_templating    = $templating;
    }

    public function onAjax(tubepress_lib_api_event_EventInterface $ajaxEvent)
    {
        $isDebugEnabled = $this->_logger->isEnabled();

        if ($isDebugEnabled) {

            $this->_logger->debug('Handling incoming request. First parsing shortcode.');
        }

        $nvpMap = $this->_requestParams->getParamValue('tubepress_options');
        $itemId = $this->_requestParams->getParamValue('tubepress_item');

        if ($isDebugEnabled) {

            $this->_logger->debug('Requested item ID is ' . $itemId);
        }

        $this->_context->setEphemeralOptions($nvpMap);

        if ($this->_context->get(tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY)) {

            $this->_context->setEphemeralOption(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY, true);
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Now asking collector for item with ID ' . $itemId);
        }

        /* grab the item! */
        $mediaItem = $this->_collector->collectSingle($itemId);

        if ($mediaItem === null) {

            $this->_responseCode->setResponseCode(404);
            $ajaxEvent->setArgument('handled', true);
            return;
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Collector found item with ID ' . $itemId . '. Sending it to browser');
        }

        $playerHtml = $this->_templating->renderTemplate('gallery/player/ajax', array(

            tubepress_app_api_template_VariableNames::MEDIA_ITEM => $mediaItem
        ));

        $toReturn = array(

            'mediaItem' => $mediaItem->toHtmlSafeArray(),
            'html'      => $playerHtml
        );

        $this->_responseCode->setResponseCode(200);

        print json_encode($toReturn);

        $ajaxEvent->setArgument('handled', true);
    }
}