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
 * @covers tubepress_app_shortcode_ioc_ShortcodeExtension
 */
class tubepress_test_app_shortcode_ioc_ShortcodeExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_app_shortcode_ioc_ShortcodeExtension
     */
    protected function buildSut()
    {
        return  new tubepress_app_shortcode_ioc_ShortcodeExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_app_shortcode_api_ParserInterface::_,
            'tubepress_app_shortcode_impl_Parser'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_shortcode', array(

            'defaultValues' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD => 'tubepress',
            ),

            'labels' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD     => 'Shortcode keyword',  //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD     => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_shortcode', array(

            'priority' => 30000,
            'map'      => array(
                'oneOrMoreWordChars' => array(
                    tubepress_app_shortcode_api_Constants::OPTION_KEYWORD
                )
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(
            tubepress_platform_api_log_LoggerInterface::_ => $logger,
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_ => tubepress_platform_api_util_StringUtilsInterface::_
        );
    }
}
