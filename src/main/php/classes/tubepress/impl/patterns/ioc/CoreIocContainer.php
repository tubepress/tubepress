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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
final class tubepress_impl_patterns_ioc_CoreIocContainer implements ehough_iconic_api_IContainer
{
    /**
     * @var ehough_iconic_impl_ContainerBuilder
     */
    private $_delegate;

    public function __construct()
    {
        $this->_delegate = new ehough_iconic_impl_ContainerBuilder();

        $this->_registerEnvironmentDetector();
        $this->_registerFilesystemFinderFactory();
        $this->_registerPluginDiscoverer();
        $this->_registerPluginRegistry();
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
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
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
    public function has($id)
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
    public function getParameter($name)
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
    public function hasParameter($name)
    {
        return $this->_delegate->hasParameter($name);
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
    public function set($id, $service, $scope = self::SCOPE_CONTAINER)
    {
        $this->_delegate->set($id, $service, $scope);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->_delegate->setParameter($name, $value);
    }

    public function findTaggedServiceIds($tag)
    {
        return $this->_delegate->findTaggedServiceIds($tag);
    }

    public function registerExtension(ehough_iconic_api_extension_IExtension $extension)
    {
        $this->_delegate->registerExtension($extension);
    }

    public function addCompilerPass(ehough_iconic_api_compiler_ICompilerPass $pass, $type = ehough_iconic_impl_compiler_PassConfig::TYPE_BEFORE_OPTIMIZATION)
    {
        $this->_delegate->addCompilerPass($pass, $type);
    }

    public function compile()
    {
        $this->_delegate->compile();
    }

    private function _registerEnvironmentDetector()
    {
        $this->_delegate->register(

            tubepress_spi_environment_EnvironmentDetector::_,
            'tubepress_impl_environment_SimpleEnvironmentDetector'
        );
    }

    private function _registerFilesystemFinderFactory()
    {
        $this->_delegate->register(

            'ehough_fimble_api_FinderFactory',
            'ehough_fimble_impl_StandardFinderFactory'
        );
    }

    private function _registerPluginDiscoverer()
    {
        $this->_delegate->register(

            tubepress_spi_plugin_PluginDiscoverer::_,
            'tubepress_impl_plugin_FilesystemPluginDiscoverer'
        );
    }

    private function _registerPluginRegistry()
    {
        $this->_delegate->register(

            tubepress_spi_plugin_PluginRegistry::_,
            'tubepress_impl_plugin_DefaultPluginRegistry'
        );
    }
}