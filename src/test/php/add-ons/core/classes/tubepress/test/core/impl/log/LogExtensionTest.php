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
 * @covers tubepress_core_impl_log_LogExtension
 */
class tubepress_test_core_impl_log_LogExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_log_LogExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_log_LogExtension();
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
        )->withArgument('[%%datetime%%] [%%level_name%%]: %%message%%');

        $this->expectRegistration(

            tubepress_api_log_LoggerInterface::_,
            'tubepress_core_impl_log_HtmlLogger'
        )->withArgument(new tubepress_api_ioc_Reference('tubepress_impl_log_BootLogger'))
            ->withArgument(new tubepress_api_ioc_Reference('epilog.logger'))
            ->withArgument(new tubepress_api_ioc_Reference('epilog.formatter'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'epilog.logger' => 'ehough_epilog_Logger',
            'epilog.formatter' => 'ehough_epilog_formatter_LineFormatter',
            tubepress_api_log_LoggerInterface::_ => 'tubepress_core_impl_log_HtmlLogger'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $context = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::DEBUG_ON)->andReturn(true);

        $bootLogger = $this->mock('tubepress_impl_log_BootLogger');
        $bootLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $bootLogger->shouldReceive('flushTo')->once()->with(ehough_mockery_Mockery::type(tubepress_api_log_LoggerInterface::_));

        return array(

            tubepress_core_api_options_ContextInterface::_ => $context,
            'tubepress_impl_log_BootLogger' => $bootLogger
        );
    }
}
