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
 * @covers tubepress_app_log_ioc_LogExtension
 */
class tubepress_test_app_log_ioc_LogExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_app_log_ioc_LogExtension
     */
    protected function buildSut()
    {
        return  new tubepress_app_log_ioc_LogExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_platform_api_log_LoggerInterface::_,
            'tubepress_app_log_impl_HtmlLogger'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_log', array(

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

        $this->expectRegistration(
            'logging_enabled_field',
            'tubepress_app_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_log_api_Constants::OPTION_DEBUG_ON)
            ->withArgument('boolean');

        $fieldMap = array(
            tubepress_app_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED => array(
                tubepress_app_log_api_Constants::OPTION_DEBUG_ON
            )
        );

        $this->expectRegistration(
            'tubepress_app_log_impl_options_ui_FieldProvider',
            'tubepress_app_log_impl_options_ui_FieldProvider'
        )->withArgument(array())
            ->withArgument(array(new tubepress_platform_api_ioc_Reference('logging_enabled_field')))
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $context = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_app_log_api_Constants::OPTION_DEBUG_ON)->andReturn(true);

        $bootLogger = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $bootLogger->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $bootLogger->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('true');

        $mockField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_app_options_api_ContextInterface::_ => $context,
            tubepress_app_http_api_RequestParametersInterface::_ => $bootLogger,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder
        );
    }
}
