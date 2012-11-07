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
 * Handles generation of the HTML for an embedded player. This expects exactly 3 GET
 * paramters: embedName (the string name of the embedded player implementation),
 * video (the video ID to load), meta (true/false whether or not to include video meta info)
 */
class tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService extends tubepress_impl_http_AbstractPluggableAjaxCommandService
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Player Ajax Command');
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
        $executionContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $player           = tubepress_impl_patterns_ioc_KernelServiceLocator::getPlayerHtmlGenerator();
        $provider         = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoCollector();
        $qss              = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $jsonEncoder      = tubepress_impl_patterns_ioc_KernelServiceLocator::getJsonEncoder();
        $isDebugEnabled   = $this->_logger->isDebugEnabled();

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

        return array(200 => $jsonEncoder->encode($toReturn));
    }
}

