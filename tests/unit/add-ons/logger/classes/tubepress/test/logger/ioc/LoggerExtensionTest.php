<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_logger_ioc_LoggerExtension
 */
class tubepress_test_logger_ioc_LoggerExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_logger_ioc_LoggerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_logger_ioc_LoggerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerOptions();
        $this->_registerOptionsUi();
        
        $this->expectRegistration(
            tubepress_api_log_LoggerInterface::_,
            'tubepress_logger_impl_HtmlLogger'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_));
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__logger',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                    tubepress_api_options_Names::DEBUG_ON => true,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::DEBUG_ON => 'Enable debugging',   //>(translatable)<
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                    tubepress_api_options_Names::DEBUG_ON => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<

                ),
            ))->withArgument(array());
    }

    private function _registerOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_api_options_Names::DEBUG_ON,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'logger_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::ADVANCED => array(
                tubepress_api_options_Names::DEBUG_ON
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__logger',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-logger')
            ->withArgument('Logger')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $requestParams = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $requestParams->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $requestParams->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('true');

        $context = $this->mock(tubepress_api_options_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_api_options_Names::DEBUG_ON)->andReturn(true);

        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_options_ContextInterface::_         => $context,
            tubepress_api_http_RequestParametersInterface::_  => $requestParams,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
        );
    }
}
