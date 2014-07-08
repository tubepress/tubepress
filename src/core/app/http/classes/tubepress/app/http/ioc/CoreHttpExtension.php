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
class tubepress_app_http_ioc_CoreHttpExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
            tubepress_app_http_api_AjaxCommandInterface::_,
            'tubepress_app_http_impl_PrimaryAjaxHandler'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_http_api_ResponseCodeInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
           'tag'    => tubepress_app_http_api_AjaxCommandInterface::_,
           'method' => 'setAjaxCommands',
        ));

        $containerBuilder->register(
            tubepress_app_http_api_RequestParametersInterface::_,
            'tubepress_app_http_impl_RequestParameters'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_http', array(

            'defaultValues' => array(
                tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD => 'GET',
            ),

            'labels' => array(
                tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD => 'HTTP method',        //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES . '_http', array(

            'optionName' => tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD,
            'priority'   => 30000,
            'values'     => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            )
        ));

        $fieldIndex = 0;
        $containerBuilder->register(
            'http_field_' . $fieldIndex++,
            'tubepress_app_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_app_html_api_Constants::OPTION_HTTPS)
         ->addArgument('boolean');

        $containerBuilder->register(
            'http_field_' . $fieldIndex++,
            'tubepress_app_options_ui_api_FieldInterface'
        )->setFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD)
         ->addArgument('dropdown');

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('http_field_' . $x);
        }

        $fieldMap = array(
            tubepress_app_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED => array(
                tubepress_app_html_api_Constants::OPTION_HTTPS,
                tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD
            )
        );

        $containerBuilder->register(
            'tubepress_lib_http_impl_options_ui_FieldProvider',
            'tubepress_lib_http_impl_options_ui_FieldProvider'
        )->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }
}