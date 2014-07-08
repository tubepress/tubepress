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
class tubepress_app_log_ioc_LogExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_platform_api_log_LoggerInterface::_,
            'tubepress_app_log_impl_HtmlLogger'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_log', array(

            'defaultValues' => array(
                tubepress_app_log_api_Constants::OPTION_DEBUG_ON => true,
            ),

            'labels' => array(
                tubepress_app_log_api_Constants::OPTION_DEBUG_ON => 'Enable debugging',   //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_log_api_Constants::OPTION_DEBUG_ON => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
            )
        ));

        $containerBuilder->register(
            'logging_enabled_field',
            'tubepress_app_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_app_log_api_Constants::OPTION_DEBUG_ON)
         ->addArgument('boolean');

        $fieldMap = array(
            tubepress_app_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED => array(
                tubepress_app_log_api_Constants::OPTION_DEBUG_ON
            )
        );

        $containerBuilder->register(
            'tubepress_app_log_impl_options_ui_FieldProvider',
            'tubepress_app_log_impl_options_ui_FieldProvider'
        )->addArgument(array())
         ->addArgument(array(new tubepress_platform_api_ioc_Reference('logging_enabled_field')))
         ->addArgument($fieldMap)
         ->addTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }
}