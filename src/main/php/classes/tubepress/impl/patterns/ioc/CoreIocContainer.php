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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
final class tubepress_impl_patterns_ioc_CoreIocContainer implements ehough_iconic_ContainerInterface
{
    /**
     * @var ehough_iconic_ContainerBuilder
     */
    private $_delegate;

    public function __construct()
    {
        $this->_delegate = new ehough_iconic_ContainerBuilder();

        /**
         * Remove some advanced IOC container stuff that we don't use (yet). This makes TubePress boot about
         * 30% faster!
         */
        $compilerPassConfig = $this->_delegate->getCompilerPassConfig();
        $compilerPassConfig->setOptimizationPasses(array());
        $compilerPassConfig->setRemovingPasses(array());

        $this->_registerEnvironmentDetector();
        $this->_registerFilesystemFinderFactory();
        $this->_registerAddonDiscoverer();
        $this->_registerAddonLoader();
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

    public function registerExtension(ehough_iconic_extension_ExtensionInterface $extension)
    {
        $this->_delegate->registerExtension($extension);
    }

    public function addCompilerPass(ehough_iconic_compiler_CompilerPassInterface $pass, $type = ehough_iconic_compiler_PassConfig::TYPE_BEFORE_OPTIMIZATION)
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

        /* Allows for convenient access to this definition by IOC extensions. */
        $this->_delegate->setAlias('tubepress_impl_environment_SimpleEnvironmentDetector', tubepress_spi_environment_EnvironmentDetector::_);
    }

    private function _registerFilesystemFinderFactory()
    {
        $this->_delegate->register(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $this->_delegate->setAlias('ehough_finder_FinderFactory', 'ehough_finder_FinderFactoryInterface');
    }

    private function _registerAddonDiscoverer()
    {
        $this->_delegate->register(

            tubepress_spi_addon_AddonDiscoverer::_,
            'tubepress_impl_addon_FilesystemAddonDiscoverer'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $this->_delegate->setAlias('tubepress_impl_addon_FilesystemAddonDiscoverer', tubepress_spi_addon_AddonDiscoverer::_);
    }

    private function _registerAddonLoader()
    {
        $this->_delegate->register(

            tubepress_spi_addon_AddonLoader::_,
            'tubepress_impl_addon_DefaultAddonLoader'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $this->_delegate->setAlias('tubepress_impl_addon_DefaultAddonLoader', tubepress_spi_addon_AddonLoader::_);
    }

    /**
     * Enters the given scope
     *
     * @param string $name
     *
     * @throws BadMethodCallException On invocation.
     */
    public function enterScope($name)
    {
        throw new BadMethodCallException();
    }

    /**
     * Leaves the current scope, and re-enters the parent scope
     *
     * @param string $name
     *
     * @throws BadMethodCallException On invocation.
     */
    public function leaveScope($name)
    {
        throw new BadMethodCallException();
    }

    /**
     * Adds a scope to the container
     *
     * @param ehough_iconic_ScopeInterface $scope
     *
     * @throws BadMethodCallException On invocation.
     */
    public function addScope(ehough_iconic_ScopeInterface $scope)
    {
        throw new BadMethodCallException();
    }

    /**
     * Whether this container has the given scope
     *
     * @param string $name
     *
     * @return Boolean
     *
     * @throws BadMethodCallException On invocation.
     */
    public function hasScope($name)
    {
        throw new BadMethodCallException();
    }

    /**
     * Determines whether the given scope is currently active.
     *
     * It does however not check if the scope actually exists.
     *
     * @param string $name
     *
     * @return Boolean
     *
     * @throws BadMethodCallException On invocation.
     */
    public function isScopeActive($name)
    {
        throw new BadMethodCallException();
    }
}