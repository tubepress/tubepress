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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_spi_patterns_cor_Command',
));

/**
 * Handles errors from YouTube and Vimeo.
 */
abstract class org_tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandler implements org_tubepress_spi_patterns_cor_Command
{
    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    function execute($context)
    {
        $ioc             = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc              = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $currentProvider = $pc->calculateCurrentVideoProvider();

        if ($currentProvider !== $this->getProviderName()) {

            org_tubepress_impl_log_Log::log($this->getLogPrefix(), 'Not a %s response', $this->getProviderName());

            return false;
        }

        $response                  = $context->response;
        $context->messageToDisplay = $this->getMessageForResponse($response);

        return true;
    }

    /**
     * Get a user-friendly response message for this HTTP response.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @return string A user-friendly response message for this HTTP response.
     */
    protected abstract function getMessageForResponse(org_tubepress_api_http_HttpResponse $response);

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

    protected final function getLogPrefix()
    {
        return $this->getFriendlyProviderName() . ' HTTP Error Handler';
    }
}