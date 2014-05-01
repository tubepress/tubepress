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
 *
 */
class tubepress_addons_coreapiservices_impl_ioc_CoreServicesContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerEnvironment($containerBuilder);
        $this->_registerCurrentUrlService($containerBuilder);
        $this->_registerHtmlGenerator($containerBuilder);
        $this->_registerOptionsContext($containerBuilder);
        $this->_registerOptionsPersistence($containerBuilder);
    }

    private function _registerHtmlGenerator(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_html_HtmlGeneratorInterface::_,
            'tubepress_addons_coreapiservices_impl_html_HtmlGenerator'
        )->addTag(
                self::TAG_TAGGED_SERVICES_CONSUMER,
                array(
                    'tag' => tubepress_spi_shortcode_PluggableShortcodeHandlerService::_,
                    'method' => 'setPluggableShortcodeHandlers'
                )
            );
    }

    private function _registerOptionsPersistence(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_options_PersistenceInterface::_,
            'tubepress_addons_coreapiservices_impl_options_Persistence'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceBackendInterface::_));
    }

    private function _registerOptionsContext(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_options_ContextInterface::_,
            'tubepress_addons_coreapiservices_impl_options_Context'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_));
    }

    private function _registerEnvironment(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_environment_EnvironmentInterface::_,
            'tubepress_addons_coreapiservices_impl_environment_Environment'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_));
    }

    private function _registerCurrentUrlService(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_url_CurrentUrlServiceInterface::_,
            'tubepress_addons_coreapiservices_impl_url_CurrentUrlService'
        )->addArgument($_SERVER)
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_));
    }
}