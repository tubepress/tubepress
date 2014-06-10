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
 * @covers tubepress_core_html_single_ioc_SingleItemExtension
 */
class tubepress_test_core_html_single_ioc_SingleExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_core_html_single_ioc_SingleItemExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener',
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_media_provider_api_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 8000
            ));

        $this->expectRegistration(
            'tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_embedded_api_EmbeddedHtmlInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
                'method'   => 'onSingleVideoTemplate',
                'priority' => 10100
            ))->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_single', array(

            'defaultValues' => array(
                tubepress_core_html_single_api_Constants::OPTION_MEDIA_ITEM_ID => null,
            ),

            'doNotPersistNames' => array(
                tubepress_core_html_single_api_Constants::OPTION_MEDIA_ITEM_ID,
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_options_api_ReferenceInterface::_ => tubepress_core_options_api_ReferenceInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_media_provider_api_CollectorInterface::_ => tubepress_core_media_provider_api_CollectorInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_embedded_api_EmbeddedHtmlInterface::_ => tubepress_core_embedded_api_EmbeddedHtmlInterface::_
        );
    }
}