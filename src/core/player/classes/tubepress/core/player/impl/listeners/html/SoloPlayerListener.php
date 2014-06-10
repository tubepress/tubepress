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
 * HTML-generation command that implements the "solo" player command.
 */
class tubepress_core_player_impl_listeners_html_SoloPlayerListener
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
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParams;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_http_api_RequestParametersInterface $requestParams)
    {
        $this->_logger                      = $logger;
        $this->_context                     = $context;
        $this->_requestParams               = $requestParams;
    }

    public function onHtmlGeneration(tubepress_core_event_api_EventInterface $event)
    {
        if (!$this->_shouldExecute()) {

            return;
        }

        $this->_handle($event);
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    private function _shouldExecute()
    {
        $playerName  = $this->_context->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);
        $shouldLog   = $this->_logger->isEnabled();

        if ($playerName !== 'solo') {

            return false;
        }

        if ($shouldLog) {

            $this->_logger->debug('Solo player detected. Checking query string for video ID.');
        }

        /* see if we have a custom video ID set */
        $videoId = $this->_requestParams->getParamValue(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO);

        if ($videoId == '') {

            if ($shouldLog) {

                $this->_logger->debug('Solo player in use, but no video ID set in URL.');
            }

            return false;
        }

        return true;
    }

    /**
     * @return string The HTML for this shortcode handler.
     */
    private function _handle(tubepress_core_event_api_EventInterface $event)
    {
        $videoId     = $this->_requestParams->getParamValue(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO);;
        $shouldLog   = $this->_logger->isEnabled();

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        $result = $this->_context->setEphemeralOption(tubepress_core_html_single_api_Constants::OPTION_VIDEO, $videoId);

        if ($result !== null) {

            if ($shouldLog) {

                $this->_logger->debug('Could not verify video ID.');
            }
        }
    }
}
