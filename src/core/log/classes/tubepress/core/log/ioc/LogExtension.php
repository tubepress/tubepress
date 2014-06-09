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
class tubepress_core_log_ioc_LogExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'epilog.logger',
            'ehough_epilog_Logger'
        )->addArgument('TubePress');

        $containerBuilder->register(
            'epilog.formatter',
            'ehough_epilog_formatter_LineFormatter'
        )->addArgument('[%%datetime%%] [%%level_name%%]: %%message%%')
         ->addArgument('i:s.u');

        $containerBuilder->register(
            tubepress_api_log_LoggerInterface::_,
            'tubepress_core_log_impl_HtmlLogger'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('epilog.logger'))
         ->addArgument(new tubepress_api_ioc_Reference('epilog.formatter'));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_log', array(

            'defaultValues' => array(
                tubepress_core_log_api_Constants::OPTION_DEBUG_ON => true,
            ),

            'labels' => array(
                tubepress_core_log_api_Constants::OPTION_DEBUG_ON => 'Enable debugging',   //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_core_log_api_Constants::OPTION_DEBUG_ON => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
            )
        ));

        $containerBuilder->register(
            'logging_enabled_field',
            'tubepress_core_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_core_log_api_Constants::OPTION_DEBUG_ON)
         ->addArgument('boolean');

        $fieldMap = array(
            tubepress_core_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED => array(
                tubepress_core_log_api_Constants::OPTION_DEBUG_ON
            )
        );

        $containerBuilder->register(
            'tubepress_core_log_impl_options_ui_FieldProvider',
            'tubepress_core_log_impl_options_ui_FieldProvider'
        )->addArgument(array())
         ->addArgument(array(new tubepress_api_ioc_Reference('logging_enabled_field')))
         ->addArgument($fieldMap)
         ->addTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }
}