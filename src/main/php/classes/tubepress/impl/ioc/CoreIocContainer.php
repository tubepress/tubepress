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
final class tubepress_impl_ioc_CoreIocContainer extends tubepress_impl_ioc_IconicContainer
{
    public function __construct()
    {
        parent::__construct();

        $this->_registerEnvironmentDetector();
        $this->_registerFilesystemFinderFactory();
        $this->_registerAddonDiscoverer();
        $this->_registerAddonLoader();
        $this->_registerEventDispatcher();
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
        )->addArgument($this->getDelegateIconicContainerBuilder());

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