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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
class tubepress_impl_ioc_CoreIocContainer extends tubepress_impl_ioc_IconicContainer
{
    public function __construct()
    {
        parent::__construct();

        $this->_registerBootHelperAddonBooter();
        $this->_registerBootHelperAddonDiscoverer();
        $this->_registerBootHelperConfigService();
        $this->_registerBootHelperClassLoadingHelper();
        $this->_registerBootHelperIocContainerHelper();

        $this->_registerEnvironmentDetector();
        $this->_registerEventDispatcher();
        $this->_registerFilesystemFinderFactory();
    }

    private function _registerEnvironmentDetector()
    {
        $this->register(

            tubepress_spi_environment_EnvironmentDetector::_,
            'tubepress_impl_environment_SimpleEnvironmentDetector'
        );
    }

    private function _registerFilesystemFinderFactory()
    {
        $this->register(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }

    private function _registerEventDispatcher()
    {
        $this->register(

            'ehough_tickertape_ContainerAwareEventDispatcher',
            'ehough_tickertape_ContainerAwareEventDispatcher'
        )->addArgument(new tubepress_impl_ioc_Reference('service_container'));

        $this->register(

            tubepress_api_event_EventDispatcherInterface::_,
            'tubepress_impl_event_DefaultEventDispatcher'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));
    }

    private function _registerBootHelperAddonBooter()
    {
        $this->register(

            tubepress_spi_boot_AddonBooter::_,
            'tubepress_impl_boot_DefaultAddonBooter'
        );
    }

    private function _registerBootHelperAddonDiscoverer()
    {
        $this->register(

            tubepress_spi_boot_AddonDiscoverer::_,
            'tubepress_impl_boot_DefaultAddonDiscoverer'
        );
    }

    private function _registerBootHelperConfigService()
    {
        $this->register(

            tubepress_spi_boot_BootConfigService::_,
            'tubepress_impl_boot_DefaultBootConfigService'
        );
    }

    private function _registerBootHelperClassLoadingHelper()
    {
        $this->register(

            tubepress_spi_boot_ClassLoadingHelper::_,
            'tubepress_impl_boot_DefaultClassLoadingHelper'
        );
    }

    private function _registerBootHelperIocContainerHelper()
    {
        $this->register(

            tubepress_spi_boot_IocContainerHelper::_,
            'tubepress_impl_boot_DefaultIocContainerBootHelper'
        );
    }
}