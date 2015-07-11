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
 * @covers tubepress_gallery_ioc_GalleryExtension
 */
class tubepress_test_gallery_ioc_GalleryExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_gallery_ioc_GalleryExtension
     */
    protected function buildSut()
    {
        return  new tubepress_gallery_ioc_GalleryExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerTemplatePathProvider();
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__gallery',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/gallery/templates'
        ))->withTag('tubepress_lib_api_template_PathProviderInterface');
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_gallery_impl_listeners_PaginationListener',
            'tubepress_gallery_impl_listeners_PaginationListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 96000,
                'method'   => 'onGalleryTemplatePreRender'
            ));

        $this->expectRegistration(
            'tubepress_gallery_impl_listeners_GalleryListener',
            'tubepress_gallery_impl_listeners_GalleryListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_CollectorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 100000,
                'method'   => 'onGalleryTemplatePreRender'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.gallery/main',
                'priority' => 100000,
                'method'   => 'onPostGalleryTemplateRender'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
                'priority' => 100000,
                'method'   => 'onGalleryInitJs'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::HTML_GENERATION,
                'priority' => 92000,
                'method'   => 'onHtmlGeneration'));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_app_api_options_ContextInterface::_        => tubepress_app_api_options_ContextInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_    => tubepress_platform_api_url_UrlFactoryInterface::_,
            tubepress_lib_api_http_RequestParametersInterface::_ => tubepress_lib_api_http_RequestParametersInterface::_,
            tubepress_app_api_options_ReferenceInterface::_      => tubepress_app_api_options_ReferenceInterface::_,
            tubepress_lib_api_event_EventDispatcherInterface::_  => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_    => tubepress_lib_api_template_TemplatingInterface::_,
            'tubepress_theme_impl_CurrentThemeService'           => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_lib_api_translation_TranslatorInterface::_ => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_platform_api_log_LoggerInterface::_        => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_app_api_media_CollectorInterface::_        => tubepress_app_api_media_CollectorInterface::_,
        );
    }
}
