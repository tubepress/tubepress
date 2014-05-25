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
 * @covers tubepress_core_impl_embedded_EmbeddedExtension
 */
class tubepress_test_core_impl_embedded_EmbeddedExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_impl_embedded_EmbeddedExtension
     */
    protected function buildSut()
    {
        return new tubepress_core_impl_embedded_EmbeddedExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_api_embedded_EmbeddedHtmlInterface::_,
            'tubepress_core_impl_embedded_EmbeddedHtml'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_api_provider_VideoProviderInterface::_,
                'method' => 'setVideoProviders'))
            ->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_api_embedded_EmbeddedProviderInterface::_,
                'method' => 'setEmbeddedProviders'));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_embedded_EmbeddedHtmlInterface::_ => 'tubepress_core_impl_embedded_EmbeddedHtml'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);
        return array(

            tubepress_api_log_LoggerInterface::_                    => $logger,
            tubepress_core_api_options_ContextInterface::_          => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_    => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_url_UrlFactoryInterface::_           => tubepress_core_api_url_UrlFactoryInterface::_,
            tubepress_core_api_template_TemplateFactoryInterface::_ => tubepress_core_api_template_TemplateFactoryInterface::_
        );
    }
}