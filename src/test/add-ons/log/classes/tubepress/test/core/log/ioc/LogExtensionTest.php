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
 * @covers tubepress_core_log_ioc_LogExtension
 */
class tubepress_test_core_log_ioc_LogExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_log_ioc_LogExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_log_ioc_LogExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'epilog.logger',
            'ehough_epilog_Logger'
        )->withArgument('TubePress');

        $this->expectRegistration(
            'epilog.formatter',
            'ehough_epilog_formatter_LineFormatter'
        )->withArgument('[%%datetime%%] [%%level_name%%]: %%message%%')
         ->withArgument('i:s.u');


        $this->expectRegistration(
            tubepress_api_log_LoggerInterface::_,
            'tubepress_core_log_impl_HtmlLogger'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('epilog.logger'))
            ->withArgument(new tubepress_api_ioc_Reference('epilog.formatter'));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_log', array(

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
    }

    protected function getExpectedExternalServicesMap()
    {
        $context = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_core_log_api_Constants::OPTION_DEBUG_ON)->andReturn(true);

        $bootLogger = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $bootLogger->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $bootLogger->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('true');

        return array(

            tubepress_core_options_api_ContextInterface::_ => $context,
            tubepress_core_http_api_RequestParametersInterface::_ => $bootLogger
        );
    }
}
