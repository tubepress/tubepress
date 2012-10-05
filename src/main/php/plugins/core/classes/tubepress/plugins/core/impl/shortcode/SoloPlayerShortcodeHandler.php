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
 * HTML-generation command that implements the "solo" player command.
 */
class tubepress_plugins_core_impl_shortcode_SoloPlayerShortcodeHandler implements tubepress_spi_shortcode_ShortcodeHandler
{
    /**
     * @var ehough_epilog_api_ILogger
     */
    private $_logger;

    /**
     * @var tubepress_spi_shortcode_ShortcodeHandler
     */
    private $_singleVideoShortcodeHandler;

    public function __construct(tubepress_spi_shortcode_ShortcodeHandler $singleVideoShortcodeHandler)
    {
        $this->_logger                      = ehough_epilog_api_LoggerFactory::getLogger('Solo Player Command');
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
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $playerName  = $execContext->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);

        if ($playerName !== 'solo') {

            return false;
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Solo player detected. Checking query string for video ID.');
        }

        /* see if we have a custom video ID set */
        $qss     = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $videoId = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);

        if ($videoId == '') {

            if ($this->_logger->isDebugEnabled()) {

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
        $qss         = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $videoId     = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);;

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        $result = $execContext->set(tubepress_api_const_options_names_Output::VIDEO, $videoId);

        if ($result !== true) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('Could not verify video ID.');
            }

            return false;
        }

        /* display the results as a thumb gallery */
        return $this->_singleVideoShortcodeHandler->getHtml();
    }
}
