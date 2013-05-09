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
final class tubepress_impl_ioc_CoreIocContainer extends ehough_iconic_ContainerBuilder implements tubepress_api_ioc_ContainerInterface
{
    public function __construct()
    {
        parent::__construct();

        /**
         * Remove some advanced IOC container stuff that we don't use (yet). This makes TubePress boot about
         * 30% faster!
         */
        $compilerPassConfig = $this->getCompilerPassConfig();
        $compilerPassConfig->setOptimizationPasses(array());
        $compilerPassConfig->setRemovingPasses(array());

        /**
         * Turn off resource loading.
         */
        $this->setResourceTracking(false);

        $this->_registerEnvironmentDetector();
        $this->_registerFilesystemFinderFactory();
        $this->_registerAddonDiscoverer();
        $this->_registerAddonLoader();
        $this->_registerEventDispatcher();
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_ContainerExtensionInterface $extension
     */
    public function registerExtension(tubepress_api_ioc_ContainerExtensionInterface $extension)
    {
        $iconicExtension = new tubepress_impl_ioc_IconicContainerExtensionWrapper($extension);

        parent::registerExtension($iconicExtension);
        $this->loadFromExtension($iconicExtension->getAlias());
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_CompilerPassInterface $pass
     *
     * @return tubepress_api_ioc_ContainerInterface The current instance.
     */
    public function addCompilerPass(tubepress_api_ioc_CompilerPassInterface $pass)
    {
        return parent::addCompilerPass(new tubepress_impl_ioc_IconicCompilerPassWrapper($pass));
    }

    /**
     * Gets a service definition.
     *
     * @param string $id The service identifier
     *
     * @return tubepress_api_ioc_Definition A tubepress_api_ioc_Definition instance, or null if the
     *                                      service does not exist.
     *
     * @api
     * @since 3.1.0
     */
    public function getDefinition($id)
    {
        try {

            return parent::getDefinition($id);

        } catch (ehough_iconic_exception_InvalidArgumentException $e) {

            return null;
        }
    }

    /**
     * Registers a service definition.
     *
     * This methods allows for simple registration of service definition
     * with a fluid interface.
     *
     * @param string $id    The service identifier
     * @param string $class The service class
     *
     * @return tubepress_api_ioc_Definition A tubepress_api_ioc_Definition instance
     *
     * @api
     * @since 3.1.0
     */
    public function register($id, $class = null)
    {
        return $this->setDefinition(strtolower($id), new tubepress_api_ioc_Definition($class));
    }

    /**
     * Sets a service definition.
     *
     * @param string                       $id         The service identifier
     * @param tubepress_api_ioc_Definition $definition A tubepress_api_ioc_Definition instance
     *
     * @return tubepress_api_ioc_Definition the service definition
     *
     * @throws BadMethodCallException When this ContainerBuilder is frozen
     *
     * @api
     * @since 3.1.0
     */
    public function setDefinition($id, tubepress_api_ioc_Definition $definition)
    {
        try {

            return parent::setDefinition($id, $definition);

        } catch (ehough_iconic_exception_BadMethodCallException $e) {

            throw new BadMethodCallException($e);
        }
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     *
     * @return object The associated service, or null if not defined.
     *
     * @throws RuntimeException If there was a problem retrieving the service.
     *
     * @api
     * @since 3.1.0
     */
    public function get($id, $invalidBehavior = ehough_iconic_ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (!$this->has($id)) {

            return null;
        }

        try {

            return parent::get($id);

        } catch (Exception $e) {

            throw new RuntimeException($e);
        }
    }

    private function _registerEnvironmentDetector()
    {
        $definition = $this->register(

            tubepress_spi_environment_EnvironmentDetector::_,
            'tubepress_impl_environment_SimpleEnvironmentDetector'
        );

        $this->setDefinition('tubepress_impl_environment_SimpleEnvironmentDetector', $definition);
    }

    private function _registerFilesystemFinderFactory()
    {
        $definition = $this->register(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );

        $this->setDefinition('ehough_finder_FinderFactory', $definition);
    }

    private function _registerEventDispatcher()
    {
        $delegate = $this->register(

            'ehough_tickertape_ContainerAwareEventDispatcher',
            'ehough_tickertape_ContainerAwareEventDispatcher'
        )->addArgument($this);

        $definition = $this->register(

            tubepress_api_event_EventDispatcherInterface::_,
            'tubepress_impl_event_DefaultEventDispatcher'
        )->addArgument($delegate);

        $this->setDefinition('tubepress_impl_event_DefaultEventDispatcher', $definition);
    }

    private function _registerAddonDiscoverer()
    {
        $definition = $this->register(

            tubepress_spi_addon_AddonDiscoverer::_,
            'tubepress_impl_addon_FilesystemAddonDiscoverer'
        );

        $this->setDefinition('tubepress_impl_addon_FilesystemAddonDiscoverer', $definition);
    }

    private function _registerAddonLoader()
    {
        $definition = $this->register(

            tubepress_spi_addon_AddonLoader::_,
            'tubepress_impl_addon_DefaultAddonLoader'
        );

        $this->setDefinition('tubepress_impl_addon_DefaultAddonLoader', $definition);
    }
}