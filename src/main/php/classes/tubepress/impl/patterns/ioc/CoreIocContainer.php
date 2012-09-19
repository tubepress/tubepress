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
 * Core services IOC container.
 */
final class tubepress_impl_patterns_ioc_CoreIocContainer implements ehough_iconic_api_IContainer
{
    const SERVICE_BOOTSTRAPPER                = 'bootStrapper';
    const SERVICE_CACHE                       = 'cacheService';
    const SERVICE_ENVIRONMENT_DETECTOR        = 'environmentDetector';
    const SERVICE_EVENT_DISPATCHER            = 'eventDispatcher';
    const SERVICE_EXECUTIION_CONTEXT          = 'executionContext';
    const SERVICE_FEED_FETCHER                = 'feedFetcher';
    const SERVICE_FEED_INSPECTOR              = 'feedInspector';
    const SERVICE_FILESYSTEM                  = 'fileSystem';
    const SERVICE_FILESYSTEM_FINDER_FACTORY   = 'fileSystemFinderFactory';
    const SERVICE_HTTP_CLIENT                 = 'httpClient';
    const SERVICE_HTTP_RESPONSE_HANDLER       = 'httpResponseHandler';
    const SERVICE_HTTP_REQUEST_PARAMS         = 'httpRequestParameterService';
    const SERVICE_MESSAGE                     = 'messageService';
    const SERVICE_OPTIONS_UI_FIELDBUILDER     = 'optionsUiFieldBuilder';
    const SERVICE_OPTIONS_UI_FORMHANDLER      = 'optionsUiFormHandler';
    const SERVICE_OPTION_DESCRIPTOR_REFERENCE = 'optionDescriptorReference';
    const SERVICE_OPTION_STORAGE_MANAGER      = 'optionStorageManager';
    const SERVICE_OPTION_VALIDATOR            = 'optionValidator';
    const SERVICE_PLAYER_HTML_GENERATOR       = 'playerHtmlGenerator';
    const SERVICE_SHORTCODE_HTML_GENERATOR    = 'shortcodeHtmlGenerator';
    const SERVICE_SHORTCODE_PARSER            = 'shortcodeParser';
    const SERVICE_THEME_HANDLER               = 'themeHandler';
    const SERVICE_URL_BUILDER                 = 'urlBuilder';
    const SERVICE_VIDEO_FACTORY               = 'videoFactory';
    const SERVICE_VIDEO_PROVIDER              = 'videoProvider';
    const SERVICE_VIDEO_PROVIDER_CALCULATOR   = 'videoProviderCalculator';
    const SERVICE_WORDPRESS_FUNCTION_WRAPPER  = 'wordPressFunctionWrapper';
    
    /**
     * @var ehough_iconic_api_IContainer
     */
    private $_delegate;

    public function __construct()
    {
        //$this->_delegate = $this->_buildDelegate();
    }

    private function _buildDelegate()
    {
        $this->_delegate = new ehough_iconic_impl_ContainerBuilder();
    }

    /**
     * Sets a service.
     *
     * @param string $id      The service identifier
     * @param object $service The service instance
     * @param string $scope   The scope of the service
     *
     * @return void
     */
    public final function set($id, $service, $scope = self::SCOPE_CONTAINER)
    {
        //ignore - this is read only!
    }

    /**
     * Gets a service.
     *
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws InvalidArgumentException if the service is not defined
     */
    public final function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->_delegate->get($id, $invalidBehavior);
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return boolean True if the service is defined, false otherwise
     */
    public final function has($id)
    {
        return $this->_delegate->has($id);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public final function getParameter($name)
    {
        return $this->_delegate->getParameter($name);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return boolean The presence of parameter in container
     */
    public final function hasParameter($name)
    {
        return $this->_delegate->hasParameter($name);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @return void
     */
    public final function setParameter($name, $value)
    {
        //ignore - this is read only!
    }
}
