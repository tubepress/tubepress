<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles errors from YouTube and Vimeo.
 */
abstract class tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandler implements ehough_chaingang_api_Command
{
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
    public final function execute(ehough_chaingang_api_Context $context)
    {
        $response = $context->get(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_RESPONSE);

        if (! $this->canHandleResponse($response)) {

            if ($this->getLogger()->isDebugEnabled()) {

                $this->getLogger()->debug(sprintf('Not a %s response', $this->getProviderName()));
            }

            return false;
        }

        $messageToReturn = $this->getMessageForResponse($response);

        $context->put(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_ERROR_MESSAGE, $messageToReturn);

        return true;
    }

    protected abstract function canHandleResponse(ehough_shortstop_api_HttpResponse $response);

    /**
     * Get a user-friendly response message for this HTTP response.
     *
     * @param ehough_shortstop_api_HttpResponse $response The HTTP response.
     *
     * @return string A user-friendly response message for this HTTP response.
     */
    protected abstract function getMessageForResponse(ehough_shortstop_api_HttpResponse $response);

    /**
     * Get the name of the provider that this command handles.
     *
     * @return string youtube|vimeo
     */
    protected abstract function getProviderName();

    /**
     * Get a logging friendly name for this handler.
     *
     * @return string A logging friendly name for this handler.
     */
    protected abstract function getFriendlyProviderName();

    /**
     * Gets the logger.
     *
     * @return mixed ehough_epilog_api_ILogger
     */
    protected abstract function getLogger();
}