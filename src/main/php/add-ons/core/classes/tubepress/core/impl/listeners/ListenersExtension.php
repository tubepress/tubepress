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
 *
 */
class tubepress_core_impl_listeners_ListenersExtension implements tubepress_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerCssJsListeners($containerBuilder);
        $this->_registerHtmlListeners($containerBuilder);
        $this->_registerHttpListeners($containerBuilder);
        $this->_registerOptionsListeners($containerBuilder);
        $this->_registerTemplateListeners($containerBuilder);
        $this->_registerVideoGalleryPageListeners($containerBuilder);
        $this->_registerStringMagicListeners($containerBuilder);
    }

    private function _registerCssJsListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_impl_listeners_html_cssjs_BaseUrlSetter',
            'tubepress_core_impl_listeners_html_cssjs_BaseUrlSetter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG,
            'method'   => 'onJsConfig',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams',
            'tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::CSS_JS_GALLERY_INIT,
            'method'   => 'onGalleryInitJs',
            'priority' => 10000)
        )->addTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_api_player_PlayerLocationInterface::_,
            'method' => 'setPluggablePlayerLocations'
        ));
    }

    private function _registerHtmlListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_impl_listeners_html_JsConfig',
            'tubepress_core_impl_listeners_html_JsConfig'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_SCRIPTS_PRE,
            'method'   => 'onPreScriptsHtml',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_PreCssHtmlListener',
            'tubepress_core_impl_listeners_html_PreCssHtmlListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_STYLESHEETS_PRE,
            'method'   => 'onBeforeCssHtml',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_ThumbGalleryBaseJs',
            'tubepress_core_impl_listeners_html_ThumbGalleryBaseJs'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryHtml',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_generation_SearchInputListener',
            'tubepress_core_impl_listeners_html_generation_SearchInputListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
            'method'   => 'onHtmlGeneration',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_generation_SearchOutputListener',
            'tubepress_core_impl_listeners_html_generation_SearchOutputListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_core_impl_listeners_html_generation_ThumbGalleryListener'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
            'method'   => 'onHtmlGeneration',
            'priority' => 9000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_generation_SingleVideoListener',
            'tubepress_core_impl_listeners_html_generation_SingleVideoListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
            'method'   => 'onHtmlGeneration',
            'priority' => 8000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_generation_SoloPlayerListener',
            'tubepress_core_impl_listeners_html_generation_SoloPlayerListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_core_impl_listeners_html_generation_SingleVideoListener'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
             'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
             'method'   => 'onHtmlGeneration',
             'priority' => 7000
         ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_html_generation_ThumbGalleryListener',
            'tubepress_core_impl_listeners_html_generation_ThumbGalleryListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
            'method'   => 'onHtmlGeneration',
            'priority' => 4000
        ));
    }

    private function _registerHttpListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_core_impl_listeners_http_ApiCacheBeforeListener',
            'tubepress_core_impl_listeners_http_ApiCacheBeforeListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTTP_REQUEST,
            'method'   => 'onEvent',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_http_ApiCacheAfterListener',
            'tubepress_core_impl_listeners_http_ApiCacheAfterListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTTP_RESPONSE,
            'method'   => 'onEvent',
            'priority' => 10000
        ));
    }

    private function _registerOptionsListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_impl_listeners_options_LegacyThemeListener',
            'tubepress_core_impl_listeners_options_LegacyThemeListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_core_api_const_options_Names::THEME,
            'method'   => 'onPreValidationSet',
            'priority' => 300000
        ));
    }

    private function _registerTemplateListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_core_impl_listeners_template_EmbeddedCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            'method'   => 'onEmbeddedTemplate',
            'priority' => 10100
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_core_impl_listeners_template_PlayerLocationCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_embedded_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION,
            'method'   => 'onPlayerTemplate',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_core_impl_listeners_template_SearchInputCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT,
            'method'   => 'onSearchInputTemplate',
            'priority' => 10000
         ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_impl_listeners_template_SingleVideoCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_embedded_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO,
            'method'   => 'onSingleVideoTemplate',
            'priority' => 10100
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_SingleVideoMeta',
            'tubepress_core_impl_listeners_template_SingleVideoMeta'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_impl_options_MetaOptionNameService::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO,
            'method'   => 'onSingleVideoTemplate',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables',
            'tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
             'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
             'method'   => 'onGalleryTemplate',
             'priority' => 10400
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName',
            'tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10300
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_ThumbGalleryPagination',
            'tubepress_core_impl_listeners_template_ThumbGalleryPagination'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_util_UrlUtilsInterface ::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
             'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
             'method'   => 'onGalleryTemplate',
             'priority' => 10200
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation',
            'tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_player_PlayerHtmlInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10100
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_template_ThumbGalleryVideoMeta',
            'tubepress_core_impl_listeners_template_ThumbGalleryVideoMeta'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_impl_options_MetaOptionNameService::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
             'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
             'method'   => 'onGalleryTemplate',
             'priority' => 10000
        ));
    }

    private function _registerVideoGalleryPageListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_core_impl_listeners_videogallerypage_PerPageSorter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10300
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_core_impl_listeners_videogallerypage_ResultCountCapper'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10100
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_core_impl_listeners_videogallerypage_VideoBlacklist'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10200
        ));

        $containerBuilder->register(

            'tubepress_core_impl_listeners_videogallerypage_VideoPrepender',
            'tubepress_core_impl_listeners_videogallerypage_VideoPrepender'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10000
       ));
    }

    private function _registerStringMagicListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_impl_listeners_StringMagicFilter_preValidation',
            'tubepress_core_impl_listeners_StringMagicFilter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET,
            'method'   => 'magic',
            'priority' => 10100
        ));

        $containerBuilder->register(
            'tubepress_core_impl_listeners_StringMagicFilter_readFromExternal',
            'tubepress_core_impl_listeners_StringMagicFilter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER,  array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT,
            'method'   => 'magic',
            'priority' => 10000
        ));
    }
}