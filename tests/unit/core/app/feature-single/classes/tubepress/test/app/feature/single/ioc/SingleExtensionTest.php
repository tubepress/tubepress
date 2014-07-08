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
 * @covers tubepress_app_feature_single_ioc_SingleItemExtension
 */
class tubepress_test_app_feature_single_ioc_SingleExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_platform_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_app_feature_single_ioc_SingleItemExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_feature_single_impl_listeners_html_SingleVideoListener',
            'tubepress_app_feature_single_impl_listeners_html_SingleVideoListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_media_provider_api_CollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 8000
            ));

        $this->expectRegistration(
            'tubepress_app_feature_single_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_app_feature_single_impl_listeners_template_SingleVideoCoreVariables'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_embedded_api_EmbeddedHtmlInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
                'method'   => 'onSingleVideoTemplate',
                'priority' => 10100
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_single', array(

            'defaultValues' => array(
                tubepress_app_feature_single_api_Constants::OPTION_MEDIA_ITEM_ID => null,
            ),

            'doNotPersistNames' => array(
                tubepress_app_feature_single_api_Constants::OPTION_MEDIA_ITEM_ID,
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_lib_translation_api_TranslatorInterface::_ => tubepress_lib_translation_api_TranslatorInterface::_,
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_app_options_api_ReferenceInterface::_ => tubepress_app_options_api_ReferenceInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_platform_api_log_LoggerInterface::_ => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_app_media_provider_api_CollectorInterface::_ => tubepress_app_media_provider_api_CollectorInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_,
            tubepress_app_embedded_api_EmbeddedHtmlInterface::_ => tubepress_app_embedded_api_EmbeddedHtmlInterface::_
        );
    }
}