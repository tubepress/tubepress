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
 * @covers tubepress_single_ioc_SingleExtension
 */
class tubepress_test_single_ioc_SingleExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_single_ioc_SingleExtension
     */
    protected function buildSut()
    {
        return  new tubepress_single_ioc_SingleExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerTemplatePathProvider();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_single_impl_listeners_SingleItemListener',
            'tubepress_single_impl_listeners_SingleItemListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_CollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::HTML_GENERATION,
                'priority' => 94000,
                'method'   => 'onHtmlGeneration'
            ));
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__single',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/single/templates',
        ))->withTag('tubepress_lib_api_template_PathProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_platform_api_log_LoggerInterface::_     => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_app_api_options_ContextInterface::_     => tubepress_app_api_options_ContextInterface::_,
            tubepress_app_api_media_CollectorInterface::_     => tubepress_app_api_media_CollectorInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_ => tubepress_lib_api_template_TemplatingInterface::_,
        );
    }
}
