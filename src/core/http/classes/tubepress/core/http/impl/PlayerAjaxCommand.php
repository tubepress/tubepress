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
 * Handles generation of the HTML for an embedded player. This expects exactly 3 GET
 * paramters: embedName (the string name of the embedded player implementation),
 * video (the video ID to load), meta (true/false whether or not to include video meta info)
 */
class tubepress_core_http_impl_PlayerAjaxCommand implements tubepress_core_http_api_AjaxCommandInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    /**
     * @var tubepress_core_media_provider_api_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_core_http_api_ResponseCodeInterface
     */
    private $_responseCode;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_player_api_PlayerHtmlInterface        $playerHtml,
                                tubepress_core_media_provider_api_CollectorInterface $collector,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_http_api_ResponseCodeInterface        $responseCode)
    {
        $this->_logger         = $logger;
        $this->_context        = $context;
        $this->_playerHtml     = $playerHtml;
        $this->_collector = $collector;
        $this->_requestParams  = $requestParams;
        $this->_responseCode   = $responseCode;
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

        $nvpMap = $this->_requestParams->getAllParams();
        $itemId = $this->_requestParams->getParamValue(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO);

        if ($isDebugEnabled) {

            $this->_logger->debug('Requested item ID is ' . $itemId);
        }

        $this->_context->setEphemeralOptions($nvpMap);

        if ($this->_context->get(tubepress_core_embedded_api_Constants::OPTION_LAZYPLAY)) {

            $this->_context->setEphemeralOption(tubepress_core_embedded_api_Constants::OPTION_AUTOPLAY, true);
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

            'title' => $mediaItem->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE),
            'html'  => $this->_playerHtml->getHtml($mediaItem)
        );

        $this->_responseCode->setResponseCode(200);

        print json_encode($toReturn);
    }
}

