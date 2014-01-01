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
class tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService extends tubepress_impl_http_AbstractPluggableAjaxCommandService
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Player Ajax Command');
    }

    /**
     * @return string The command name that this handler responds to.
     */
    public final function getName()
    {
        return 'playerHtml';
    }

    protected function getStatusCodeToHtmlMap()
    {
        $executionContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $player           = tubepress_impl_patterns_sl_ServiceLocator::getPlayerHtmlGenerator();
        $provider         = tubepress_impl_patterns_sl_ServiceLocator::getVideoCollector();
        $qss              = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $isDebugEnabled   = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($isDebugEnabled) {

            $this->_logger->debug('Handling incoming request. First parsing shortcode.');
        }

        $nvpMap  = $qss->getAllParams();
        $videoId = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);

        if ($isDebugEnabled) {

            $this->_logger->debug('Requested video is ' . $videoId);
        }

        $executionContext->setCustomOptions($nvpMap);

        if ($executionContext->get(tubepress_api_const_options_names_Embedded::LAZYPLAY)) {

            $executionContext->set(tubepress_api_const_options_names_Embedded::AUTOPLAY, true);
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Now asking video collector for video ' . $videoId);
        }

        /* grab the video! */
        $video = $provider->collectSingleVideo($videoId);

        if ($video === null) {

            return array(404 => "Video $videoId not found");
        }

        if ($isDebugEnabled) {

            $this->_logger->debug('Video collector found video ' . $videoId . '. Sending it to browser');
        }

        $toReturn = array(

            'title' => $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TITLE),
            'html'  => $player->getHtml($video)
        );

        return array(200 => json_encode($toReturn));
    }
}

