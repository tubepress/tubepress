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

class tubepress_app_player_impl_http_PlayerAjaxCommand implements tubepress_app_http_api_AjaxCommandInterface
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    /**
     * @var tubepress_app_media_provider_api_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_app_http_api_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_lib_http_api_ResponseCodeInterface
     */
    private $_responseCode;

    public function __construct(tubepress_platform_api_log_LoggerInterface                    $logger,
                                tubepress_app_options_api_ContextInterface          $context,
                                tubepress_app_player_api_PlayerHtmlInterface        $playerHtml,
                                tubepress_app_media_provider_api_CollectorInterface $collector,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_lib_http_api_ResponseCodeInterface        $responseCode)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_playerHtml    = $playerHtml;
        $this->_collector     = $collector;
        $this->_requestParams = $requestParams;
        $this->_responseCode  = $responseCode;
    }

    /**
     * @return string The command name that this handler responds to.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'playerHtml';
    }

    /**
     * Handle the Ajax request.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function handle()
    {
        $isDebugEnabled = $this->_logger->isEnabled();

        if ($isDebugEnabled) {

            $this->_logger->debug('Handling incoming request. First parsing shortcode.');
        }

        $nvpMap = $this->_requestParams->getParamValue('tubepress_options');
        $itemId = $this->_requestParams->getParamValue(tubepress_lib_http_api_Constants::PARAM_NAME_ITEMID);

        if ($isDebugEnabled) {

            $this->_logger->debug('Requested item ID is ' . $itemId);
        }

        $this->_context->setEphemeralOptions($nvpMap);

        if ($this->_context->get(tubepress_app_embedded_api_Constants::OPTION_LAZYPLAY)) {

            $this->_context->setEphemeralOption(tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY, true);
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Now asking collector for item with ID ' . $itemId);
        }

        /* grab the item! */
        $mediaItem = $this->_collector->collectSingle($itemId);

        if ($mediaItem === null) {

            $this->_responseCode->setResponseCode(404);
            echo sprintf('Video %s not found', $itemId);            //>(translatable)<
            return;
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Collector found item with ID ' . $itemId . '. Sending it to browser');
        }

        $toReturn = array(

            'item' => $mediaItem->toHtmlSafeArray(),
            'html' => $this->_playerHtml->getAjaxHtml($mediaItem)
        );

        $this->_responseCode->setResponseCode(200);

        print json_encode($toReturn);
    }
}