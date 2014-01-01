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
 * Handles incoming Ajax requests and outputs a response.
 */
class tubepress_impl_http_DefaultAjaxHandler implements tubepress_spi_http_AjaxHandler
{
    /**
     * @var ehough_epilog_Logger Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debugging enabled?
     */
    private $_isDebugEnabled;

    /**
     * @var tubepress_spi_http_PluggableAjaxCommandService[]
     */
    private $_commandHandlers = array();

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Ajax Handler');
    }

    /**
     * Handles incoming requests.
     *
     * @return void Handle the request and output a response.
     */
    public final function handle()
    {
        $this->_isDebugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('Handling incoming request');
        }

        $httpRequestParameterService = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $httpResponseCodeHandler     = tubepress_impl_patterns_sl_ServiceLocator::getHttpResponseCodeHandler();
        $actionName                  = $httpRequestParameterService->getParamValue(tubepress_spi_const_http_ParamName::ACTION);

        if ($actionName == '') {

            $httpResponseCodeHandler->setResponseCode(400);
            echo 'Missing "action" parameter';
            return;
        }

        $chosenCommandHandler = null;

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($this->_commandHandlers) . ' pluggable Ajax command service(s) registered');
        }

        /**
         * @var $commandHandler tubepress_spi_http_PluggableAjaxCommandService
         */
        foreach ($this->_commandHandlers as $commandHandler) {

            if ($commandHandler->getName() === $actionName) {

                $chosenCommandHandler = $commandHandler;

                break;
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($commandHandler->getName() . ' could not handle action ' . $actionName);
            }
        }

        if ($chosenCommandHandler === null) {

            if ($this->_isDebugEnabled) {

                $this->_logger->debug('No pluggable Ajax command services could handle action ' . $actionName);
            }

            $httpResponseCodeHandler->setResponseCode(500);

            return;
        }

        if ($this->_isDebugEnabled) {

            $this->_logger->debug($chosenCommandHandler->getName() . ' chose to handle action ' . $actionName);
        }

        $chosenCommandHandler->handle();

        $httpResponseCodeHandler->setResponseCode($chosenCommandHandler->getHttpStatusCode());

        echo $chosenCommandHandler->getOutput();
    }

    public function setPluggableAjaxCommandHandlers(array $handlers)
    {
        $this->_commandHandlers = $handlers;
    }
}