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
class tubepress_impl_shortcode_commands_SoloPlayerCommand implements ehough_chaingang_api_Command
{
    /**
     * @var ehough_epilog_api_ILogger
     */
    private $_logger;

    /**
     * @var ehough_chaingang_api_Chain
     */
    private $_chain;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Solo Player Command');
        $this->_chain  = $chain;
    }

    /**
     * Execute a unit of processing work to be performed.
     *
     * This Command may either complete the required processing and return true,
     * or delegate remaining processing to the next Command in a Chain containing
     * this Command by returning false.
     *
     * @param ehough_chaingang_api_Context $context The Context to be processed by this Command.
     *
     * @return boolean True if the processing of this Context has been completed, or false if the
     *                 processing of this Context should be delegated to a subsequent Command
     *                 in an enclosing Chain.
     */
    public function execute(ehough_chaingang_api_Context $context)
    {
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $playerName  = $execContext->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);

        if ($playerName !== tubepress_api_const_options_values_PlayerLocationValue::SOLO) {

            return false;
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Solo player detected. Checking query string for video ID.');
        }

        /* see if we have a custom video ID set */
        $qss     = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $videoId = $qss->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);;

        if ($videoId == '') {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('Solo player in use, but no video ID set in URL.');
            }

            return false;
        }

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
        return $this->_chain->execute($context);
    }
}
