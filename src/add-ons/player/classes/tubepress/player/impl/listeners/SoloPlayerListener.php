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
 * HTML-generation command that implements the "solo" player command.
 */
class tubepress_player_impl_listeners_SoloPlayerListener
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    public function __construct(tubepress_api_log_LoggerInterface             $logger,
                                tubepress_api_options_ContextInterface        $context,
                                tubepress_api_http_RequestParametersInterface $requestParams)
    {
        $this->_logger        = $logger;
        $this->_context       = $context;
        $this->_requestParams = $requestParams;
    }

    public function onHtmlGeneration(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_shouldExecute()) {

            return;
        }

        $this->_handle($event);
    }

    /**
     * @return bool True if this handler is interested in generating HTML, false otherwise.
     */
    private function _shouldExecute()
    {
        $playerName = $this->_context->get(tubepress_api_options_Names::PLAYER_LOCATION);
        $shouldLog  = $this->_logger->isEnabled();

        if ($playerName !== 'solo') {

            return false;
        }

        if ($shouldLog) {

            $this->_logger->debug('Solo player detected. Checking query string for video ID.');
        }

        /* see if we have a custom video ID set */
        $itemId = $this->_requestParams->getParamValue('tubepress_item');

        if ($itemId == '') {

            if ($shouldLog) {

                $this->_logger->debug('Solo player in use, but no video ID set in URL.');
            }

            return false;
        }

        return true;
    }

    private function _handle(tubepress_api_event_EventInterface $event)
    {
        $itemId    = $this->_requestParams->getParamValue('tubepress_item');
        $shouldLog = $this->_logger->isEnabled();

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $itemId));
        }

        $result = $this->_context->setEphemeralOption(tubepress_api_options_Names::SINGLE_MEDIA_ITEM_ID, $itemId);

        if ($result !== null) {

            if ($shouldLog) {

                $this->_logger->debug('Could not verify video ID.');
            }
        }
    }
}
