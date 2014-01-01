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
class tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService implements tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_spi_shortcode_PluggableShortcodeHandlerService
     */
    private $_singleVideoShortcodeHandler;

    public function __construct(tubepress_spi_shortcode_PluggableShortcodeHandlerService $singleVideoShortcodeHandler)
    {
        $this->_logger                      = ehough_epilog_LoggerFactory::getLogger('Solo Player Command');
        $this->_singleVideoShortcodeHandler = $singleVideoShortcodeHandler;
    }

    /**
     * @return string The name of this shortcode handler. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'solo-player';
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    public final function shouldExecute()
    {
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $playerName  = $execContext->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $shouldLog   = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($playerName !== 'solo') {

            return false;
        }

        if ($shouldLog) {

            $this->_logger->debug('Solo player detected. Checking query string for video ID.');
        }

        /* see if we have a custom video ID set */
        $qss     = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $videoId = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);

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
    public final function getHtml()
    {
        $qss         = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $videoId     = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);;
        $shouldLog   = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        $result = $execContext->set(tubepress_api_const_options_names_Output::VIDEO, $videoId);

        if ($result !== true) {

            if ($shouldLog) {

                $this->_logger->debug('Could not verify video ID.');
            }

            return false;
        }

        /* display the results as a thumb gallery */
        return $this->_singleVideoShortcodeHandler->getHtml();
    }
}
