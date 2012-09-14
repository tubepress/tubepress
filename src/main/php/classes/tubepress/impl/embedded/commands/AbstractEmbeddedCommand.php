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
 * Base class for embedded commands.
 */
abstract class tubepress_impl_embedded_commands_AbstractEmbeddedCommand implements ehough_chaingang_api_Command
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
        $providerName        = $context->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME);
        $videoId             = $context->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID);
        $execContext         = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $themeHandlerService = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();

        if (!$this->_canHandle($providerName, $videoId, $execContext)) {

            return false;
        }

        $template = $themeHandlerService->getTemplateInstance($this->_getTemplatePath($providerName, $videoId, $execContext));

        $context->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_TEMPLATE, $template);
        $context->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_DATA_URL, $this->_getEmbeddedDataUrl($providerName, $videoId, $execContext));
        $context->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_IMPLEMENTATION_NAME, $this->_getEmbeddedImplName());

        /* signal that we've handled execution */
        return true;
    }

    protected abstract function _getEmbeddedImplName();

    protected abstract function _canHandle($providerName, $videoId, tubepress_spi_context_ExecutionContext $execContext);

    protected abstract function _getTemplatePath($providerName, $videoId, tubepress_spi_context_ExecutionContext $execContext);

    protected abstract function _getEmbeddedDataUrl($providerName, $videoId, tubepress_spi_context_ExecutionContext $execContext);
}
