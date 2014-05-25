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
 * @covers tubepress_core_impl_listeners_ListenersExtension
 */
class tubepress_test_core_impl_listeners_ListenersExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_listeners_ListenersExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_listeners_ListenersExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_cssjs_BaseUrlSetter',
            'tubepress_core_impl_listeners_html_cssjs_BaseUrlSetter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG,
            'method'   => 'onJsConfig',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams',
            'tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::CSS_JS_GALLERY_INIT,
            'method'   => 'onGalleryInitJs',
            'priority' => 10000)
        )->withTag(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_api_player_PlayerLocationInterface::_,
            'method' => 'setPluggablePlayerLocations'
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_JsConfig',
            'tubepress_core_impl_listeners_html_JsConfig'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_SCRIPTS_PRE,
            'method'   => 'onPreScriptsHtml',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_PreCssHtmlListener',
            'tubepress_core_impl_listeners_html_PreCssHtmlListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_STYLESHEETS_PRE,
            'method'   => 'onBeforeCssHtml',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_ThumbGalleryBaseJs',
            'tubepress_core_impl_listeners_html_ThumbGalleryBaseJs'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryHtml',
            'priority' => 10000
        ));

        $this->expectRegistration(
            'tubepress_core_impl_listeners_options_LegacyThemeListener',
            'tubepress_core_impl_listeners_options_LegacyThemeListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_core_api_const_options_Names::THEME,
            'method'   => 'onPreValidationSet',
            'priority' => 300000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_core_impl_listeners_template_EmbeddedCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
            'method'   => 'onEmbeddedTemplate',
            'priority' => 10100
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_core_impl_listeners_template_PlayerLocationCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_embedded_EmbeddedHtmlInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION,
            'method'   => 'onPlayerTemplate',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_core_impl_listeners_template_SearchInputCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT,
            'method'   => 'onSearchInputTemplate',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_impl_listeners_template_SingleVideoCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_embedded_EmbeddedHtmlInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO,
            'method'   => 'onSingleVideoTemplate',
            'priority' => 10100
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_SingleVideoMeta',
            'tubepress_core_impl_listeners_template_SingleVideoMeta'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_impl_options_MetaOptionNameService::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO,
            'method'   => 'onSingleVideoTemplate',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables',
            'tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10400
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName',
            'tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10300
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_ThumbGalleryPagination',
            'tubepress_core_impl_listeners_template_ThumbGalleryPagination'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_util_UrlUtilsInterface ::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
             'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
             'method'   => 'onGalleryTemplate',
             'priority' => 10200
         ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation',
            'tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_player_PlayerHtmlInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10100
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_template_ThumbGalleryVideoMeta',
            'tubepress_core_impl_listeners_template_ThumbGalleryVideoMeta'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ProviderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_impl_options_MetaOptionNameService::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
            'method'   => 'onGalleryTemplate',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_core_impl_listeners_videogallerypage_PerPageSorter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10300
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_core_impl_listeners_videogallerypage_ResultCountCapper'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10100
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_core_impl_listeners_videogallerypage_VideoBlacklist'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10200
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_videogallerypage_VideoPrepender',
            'tubepress_core_impl_listeners_videogallerypage_VideoPrepender'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event' => tubepress_core_api_const_event_EventNames::VIDEO_GALLERY_PAGE,
            'method' => 'onVideoGalleryPage',
            'priority' => 10000
        ));

        $this->expectRegistration(
            'tubepress_core_impl_listeners_StringMagicFilter_preValidation',
            'tubepress_core_impl_listeners_StringMagicFilter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET,
            'method'   => 'magic',
            'priority' => 10100
        ));

        $this->expectRegistration(
            'tubepress_core_impl_listeners_StringMagicFilter_readFromExternal',
            'tubepress_core_impl_listeners_StringMagicFilter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER,  array(
            'event'    => tubepress_core_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT,
            'method'   => 'magic',
            'priority' => 10000
        ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_generation_SearchInputListener',
            'tubepress_core_impl_listeners_html_generation_SearchInputListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
                'method'   => 'onHtmlGeneration',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_generation_SearchOutputListener',
            'tubepress_core_impl_listeners_html_generation_SearchOutputListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_core_impl_listeners_html_generation_ThumbGalleryListener'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
                'method'   => 'onHtmlGeneration',
                'priority' => 9000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_generation_SingleVideoListener',
            'tubepress_core_impl_listeners_html_generation_SingleVideoListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
                'method'   => 'onHtmlGeneration',
                'priority' => 8000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_generation_SoloPlayerListener',
            'tubepress_core_impl_listeners_html_generation_SoloPlayerListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_core_impl_listeners_html_generation_SingleVideoListener'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
                'method'   => 'onHtmlGeneration',
                'priority' => 7000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_html_generation_ThumbGalleryListener',
            'tubepress_core_impl_listeners_html_generation_ThumbGalleryListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTML_GENERATION,
                'method'   => 'onHtmlGeneration',
                'priority' => 4000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_http_ApiCacheBeforeListener',
            'tubepress_core_impl_listeners_http_ApiCacheBeforeListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTTP_REQUEST,
                'method'   => 'onEvent',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_core_impl_listeners_http_ApiCacheAfterListener',
            'tubepress_core_impl_listeners_http_ApiCacheAfterListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::HTTP_RESPONSE,
                'method'   => 'onEvent',
                'priority' => 10000
            ));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'tubepress_core_impl_listeners_html_cssjs_BaseUrlSetter' => 'tubepress_core_impl_listeners_html_cssjs_BaseUrlSetter',
            'tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams' => 'tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams',
            'tubepress_core_impl_listeners_html_JsConfig' => 'tubepress_core_impl_listeners_html_JsConfig',
            'tubepress_core_impl_listeners_html_PreCssHtmlListener' => 'tubepress_core_impl_listeners_html_PreCssHtmlListener',
            'tubepress_core_impl_listeners_html_ThumbGalleryBaseJs' => 'tubepress_core_impl_listeners_html_ThumbGalleryBaseJs',
            'tubepress_core_impl_listeners_http_ApiCacheBeforeListener' => 'tubepress_core_impl_listeners_http_ApiCacheBeforeListener',
            'tubepress_core_impl_listeners_http_ApiCacheAfterListener' => 'tubepress_core_impl_listeners_http_ApiCacheAfterListener',
            'tubepress_core_impl_listeners_options_LegacyThemeListener' => 'tubepress_core_impl_listeners_options_LegacyThemeListener',
            'tubepress_core_impl_listeners_template_EmbeddedCoreVariables' => 'tubepress_core_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_core_impl_listeners_template_PlayerLocationCoreVariables' => 'tubepress_core_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_core_impl_listeners_template_SearchInputCoreVariables' => 'tubepress_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_core_impl_listeners_template_SingleVideoCoreVariables' => 'tubepress_core_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_impl_listeners_template_SingleVideoMeta' => 'tubepress_core_impl_listeners_template_SingleVideoMeta',
            'tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables' => 'tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables',
            'tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName' => 'tubepress_core_impl_listeners_template_ThumbGalleryEmbeddedImplName',
            'tubepress_core_impl_listeners_template_ThumbGalleryPagination' => 'tubepress_core_impl_listeners_template_ThumbGalleryPagination',
            'tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation' => 'tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation',
            'tubepress_core_impl_listeners_template_ThumbGalleryVideoMeta' => 'tubepress_core_impl_listeners_template_ThumbGalleryVideoMeta',
            'tubepress_core_impl_listeners_videogallerypage_PerPageSorter' => 'tubepress_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_core_impl_listeners_videogallerypage_ResultCountCapper' => 'tubepress_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_core_impl_listeners_videogallerypage_VideoBlacklist' => 'tubepress_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_core_impl_listeners_videogallerypage_VideoPrepender' => 'tubepress_core_impl_listeners_videogallerypage_VideoPrepender',
            'tubepress_core_impl_listeners_StringMagicFilter_preValidation' => 'tubepress_core_impl_listeners_StringMagicFilter',
            'tubepress_core_impl_listeners_StringMagicFilter_readFromExternal' => 'tubepress_core_impl_listeners_StringMagicFilter',
            'tubepress_core_impl_listeners_html_generation_SearchInputListener' => 'tubepress_core_impl_listeners_html_generation_SearchInputListener',
            'tubepress_core_impl_listeners_html_generation_SearchOutputListener' => 'tubepress_core_impl_listeners_html_generation_SearchOutputListener',
            'tubepress_core_impl_listeners_html_generation_SingleVideoListener' => 'tubepress_core_impl_listeners_html_generation_SingleVideoListener',
            'tubepress_core_impl_listeners_html_generation_SoloPlayerListener' => 'tubepress_core_impl_listeners_html_generation_SoloPlayerListener',
            'tubepress_core_impl_listeners_html_generation_ThumbGalleryListener' => 'tubepress_core_impl_listeners_html_generation_ThumbGalleryListener'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_api_log_LoggerInterface::_ => $logger,
            tubepress_core_api_environment_EnvironmentInterface::_ => tubepress_core_api_environment_EnvironmentInterface::_,
            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_options_ProviderInterface::_ => tubepress_core_api_options_ProviderInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_http_RequestParametersInterface::_ => tubepress_core_api_http_RequestParametersInterface::_,
            tubepress_core_api_theme_ThemeLibraryInterface::_ => tubepress_core_api_theme_ThemeLibraryInterface::_,
            tubepress_core_api_embedded_EmbeddedHtmlInterface::_ => tubepress_core_api_embedded_EmbeddedHtmlInterface::_,
            tubepress_core_api_translation_TranslatorInterface::_ => tubepress_core_api_translation_TranslatorInterface::_,
            tubepress_core_api_url_UrlFactoryInterface::_ => tubepress_core_api_url_UrlFactoryInterface::_,
            'tubepress_core_impl_options_MetaOptionNameService' => 'tubepress_core_impl_options_MetaOptionNameService',
            tubepress_core_api_template_TemplateFactoryInterface::_ => tubepress_core_api_template_TemplateFactoryInterface::_,
            tubepress_core_api_util_UrlUtilsInterface::_ => tubepress_core_api_util_UrlUtilsInterface::_,
            tubepress_core_api_player_PlayerHtmlInterface::_ => tubepress_core_api_player_PlayerHtmlInterface::_,
            tubepress_core_api_collector_CollectorInterface::_ => tubepress_core_api_collector_CollectorInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_,
            'ehough_stash_interfaces_PoolInterface' => 'ehough_stash_interfaces_PoolInterface'
        );
    }
}
