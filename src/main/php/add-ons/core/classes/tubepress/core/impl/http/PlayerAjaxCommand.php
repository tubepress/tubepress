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
class tubepress_core_impl_http_PlayerAjaxCommand implements tubepress_core_api_http_AjaxCommandInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_player_PlayerHtmlInterface
     */
    private $_playerHtml;

    /**
     * @var tubepress_core_api_collector_CollectorInterface
     */
    private $_videoCollector;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_core_api_http_ResponseCodeInterface
     */
    private $_responseCode;

    public function __construct(tubepress_api_log_LoggerInterface $logger,
                                tubepress_core_api_options_ContextInterface $context,
                                tubepress_core_api_player_PlayerHtmlInterface $playerHtml,
                                tubepress_core_api_collector_CollectorInterface $videoCollector,
                                tubepress_core_api_http_RequestParametersInterface $requestParams,
                                tubepress_core_api_http_ResponseCodeInterface $responseCode)
    {
        $this->_logger         = $logger;
        $this->_context        = $context;
        $this->_playerHtml     = $playerHtml;
        $this->_videoCollector = $videoCollector;
        $this->_requestParams  = $requestParams;
        $this->_responseCode   = $responseCode;
    }

    /**
     * @return string The command name that this handler responds to.
     *
     * @api
     * @since 4.0.0
     */
    public final function getName()
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

        $nvpMap  = $this->_requestParams->getAllParams();
        $videoId = $this->_requestParams->getParamValue(tubepress_core_api_const_http_ParamName::VIDEO);

        if ($isDebugEnabled) {

            $this->_logger->debug('Requested video is ' . $videoId);
        }

        $this->_context->setAll($nvpMap);

        if ($this->_context->get(tubepress_core_api_const_options_Names::LAZYPLAY)) {

            $this->_context->set(tubepress_core_api_const_options_Names::AUTOPLAY, true);
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Now asking video collector for video ' . $videoId);
        }

        /* grab the video! */
        $video = $this->_videoCollector->collectSingle($videoId);

        if ($video === null) {

            $this->_responseCode->setResponseCode(404);
            print "Video $videoId not found";
            return;
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Video collector found video ' . $videoId . '. Sending it to browser');
        }

        $toReturn = array(

            'title' => $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TITLE),
            'html'  => $this->_playerHtml->getHtml($video)
        );

        $this->_responseCode->setResponseCode(200);

        print json_encode($toReturn);
    }
}

