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
class tubepress_core_impl_http_PrimaryAjaxHandler implements tubepress_core_api_http_AjaxCommandInterface
{
    /**
     * @var tubepress_api_log_LoggerInterface Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debugging enabled?
     */
    private $_isDebugEnabled;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_core_api_http_ResponseCodeInterface
     */
    private $_responseCode;

    /**
     * @var tubepress_core_api_http_AjaxCommandInterface[]
     */
    private $_commandHandlers = array();

    public function __construct(tubepress_api_log_LoggerInterface $logger,
                                tubepress_core_api_http_RequestParametersInterface $requestParams,
                                tubepress_core_api_http_ResponseCodeInterface $responseCode)
    {
        $this->_logger            = $logger;
        $this->_isDebugEnabled    = $logger->isEnabled();
        $this->_requestParameters = $requestParams;
        $this->_responseCode      = $responseCode;
    }

    /**
     * Handle the Ajax request.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public final function handle()
    {
        if ($this->_isDebugEnabled) {

            $this->_logger->debug('Handling incoming request');
        }

        $actionName = $this->_requestParameters->getParamValue(self::PARAM_NAME_ACTION);

        if ($actionName == '') {

            $this->_responseCode->setResponseCode(400);
            echo 'Missing "action" parameter';
            return;
        }

        $chosenCommandHandler = null;

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($this->_commandHandlers) . ' pluggable Ajax command service(s) registered');
        }

        /**
         * @var $commandHandler tubepress_core_api_http_AjaxCommandInterface
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

            $this->_responseCode->setResponseCode(500);

            return;
        }

        if ($this->_isDebugEnabled) {

            $this->_logger->debug($chosenCommandHandler->getName() . ' chose to handle action ' . $actionName);
        }

        $chosenCommandHandler->handle();
    }

    public function setPluggableAjaxCommandHandlers(array $handlers)
    {
        $this->_commandHandlers = $handlers;
    }

    /**
     * @return string The command name that this handler responds to.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'primary';
    }
}