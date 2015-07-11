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
 *
 */
class tubepress_gallery_ioc_GalleryExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerTemplatePathProvider($containerBuilder);
    }

    private function _registerTemplatePathProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__gallery',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/gallery/templates'
        ))->addTag('tubepress_lib_api_template_PathProviderInterface');
    }
    
    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_gallery_impl_listeners_PaginationListener',
            'tubepress_gallery_impl_listeners_PaginationListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 96000,
            'method'   => 'onGalleryTemplatePreRender'
        ));

        $containerBuilder->register(
            'tubepress_gallery_impl_listeners_GalleryListener',
            'tubepress_gallery_impl_listeners_GalleryListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_media_CollectorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 100000,
            'method'   => 'onGalleryTemplatePreRender'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.gallery/main',
            'priority' => 100000,
            'method'   => 'onPostGalleryTemplateRender'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
            'priority' => 100000,
            'method'   => 'onGalleryInitJs'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::HTML_GENERATION,
            'priority' => 92000,
            'method'   => 'onHtmlGeneration'));
    }
}