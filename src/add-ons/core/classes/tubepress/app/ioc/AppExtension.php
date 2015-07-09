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
class tubepress_app_ioc_AppExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerEnvironment($containerBuilder);
        $this->_registerHtmlGenerator($containerBuilder);
        $this->_registerHttpSingletons($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerLogger($containerBuilder);
        $this->_registerMediaSingletons($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsSingletons($containerBuilder);
        $this->_registerOptionsUiSingletons($containerBuilder);
        $this->_registerOptionsUiFieldProvider($containerBuilder);
        $this->_registerPlayers($containerBuilder);
        $this->_registerShortcode($containerBuilder);
        $this->_registerTheme($containerBuilder);
        $this->_registerVendorServices($containerBuilder);
    }

    private function _registerEnvironment(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_app_api_environment_EnvironmentInterface::_,
            'tubepress_app_impl_environment_Environment'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_));
    }

    private function _registerHtmlGenerator(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_impl_html_CssAndJsGenerationHelper',
            'tubepress_app_impl_html_CssAndJsGenerationHelper'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_impl_theme_CurrentThemeService'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addArgument(tubepress_app_api_event_Events::HTML_STYLESHEETS)
         ->addArgument(tubepress_app_api_event_Events::HTML_SCRIPTS)
         ->addArgument('cssjs/styles')
         ->addArgument('cssjs/scripts');

        $containerBuilder->register(
            tubepress_app_api_html_HtmlGeneratorInterface::_,
            'tubepress_app_impl_html_HtmlGenerator'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_impl_html_CssAndJsGenerationHelper'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_));
    }

    private function _registerHttpSingletons(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_lib_api_http_AjaxInterface::_,
            'tubepress_app_impl_http_PrimaryAjaxHandler'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_ResponseCodeInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_));

        $containerBuilder->register(
            tubepress_lib_api_http_RequestParametersInterface::_,
            'tubepress_app_impl_http_RequestParameters'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_));
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $listenerData = array(

            /**
             * ADMIN GUI
             */
            'tubepress_app_impl_listeners_admingui_BootstrapIe8Listener' => array(
                tubepress_app_api_environment_EnvironmentInterface::_
            ),

            /**
             * EMBEDDED
             */
            'tubepress_app_impl_listeners_embedded_EmbeddedListener' => array(
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_,
            ),

            /**
             * GALLERY
             */
            'tubepress_app_impl_listeners_gallery_GalleryListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_,
                tubepress_app_api_media_CollectorInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_,
                tubepress_lib_api_event_EventDispatcherInterface::_,
                tubepress_app_api_options_ReferenceInterface::_,
            ),

            /**
             * HTML
             */
            'tubepress_app_impl_listeners_html_generation_SoloPlayerListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_
            ),
            'tubepress_app_impl_listeners_html_generation_SingleItemListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_app_api_media_CollectorInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_
            ),
            'tubepress_app_impl_listeners_html_exception_LoggingListener' => array(
                tubepress_platform_api_log_LoggerInterface::_
            ),
            'tubepress_app_impl_listeners_html_jsconfig_BaseUrlSetter' => array(
                tubepress_app_api_environment_EnvironmentInterface::_
            ),

            /**
             * HTTP
             */
            'tubepress_app_impl_listeners_http_ajax_PlayerAjaxCommand' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_app_api_media_CollectorInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_,
                tubepress_lib_api_http_ResponseCodeInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_
            ),
            'tubepress_app_impl_listeners_http_ApiCacheListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                'ehough_stash_interfaces_PoolInterface',
            ),
            'tubepress_app_impl_listeners_http_UserAgentListener' => array(
                tubepress_app_api_environment_EnvironmentInterface::_
            ),

            /**
             * MEDIA
             */
            'tubepress_app_impl_listeners_media_PageListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_,
                tubepress_app_api_media_CollectorInterface::_,
                tubepress_platform_api_url_UrlFactoryInterface::_,
            ),
            'tubepress_app_impl_listeners_media_CollectionListener' => array(),
            'tubepress_app_impl_listeners_media_DispatchingListener' => array(
                tubepress_lib_api_event_EventDispatcherInterface::_
            ),

            /**
             * NVP
             */
            'tubepress_app_impl_listeners_nvp_StringMagicListener' => array(
                tubepress_lib_api_event_EventDispatcherInterface::_
            ),

            /**
             * OPTIONS SET
             */
            'tubepress_app_impl_listeners_options_set_BasicOptionValidity' => array(
                tubepress_app_api_options_ReferenceInterface::_,
                tubepress_app_api_options_AcceptableValuesInterface::_,
                tubepress_lib_api_translation_TranslatorInterface::_,
                tubepress_platform_api_util_LangUtilsInterface::_
            ),
            'tubepress_app_impl_listeners_options_set_LegacyThemeListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_
            ),
            'tubepress_app_impl_listeners_options_set_LoggingListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_platform_api_util_StringUtilsInterface::_
            ),

            /**
             * OPTIONS VALUES
             */
            'tubepress_app_impl_listeners_options_values_FeedOptions'    => array(),
            'tubepress_app_impl_listeners_options_values_PerPageSort'    => array(),
            'tubepress_app_impl_listeners_options_values_ThemeListener' => array(
                tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_
            ),

            /**
             * PLAYER
             */
            'tubepress_app_impl_listeners_player_PlayerListener' => array(
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_,
            ),

            /**
             * SEARCH
             */
            'tubepress_app_impl_listeners_search_SearchListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_,
            ),

            /**
             * TEMPLATE POST
             */
            'tubepress_app_impl_listeners_template_post_CssJsPostListener' => array(
                tubepress_lib_api_event_EventDispatcherInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_
            ),

            /**
             * TEMPLATE PRE
             */
            'tubepress_app_impl_listeners_template_pre_MetaDisplayListener' => array(
                tubepress_app_api_options_ContextInterface::_,
                tubepress_app_api_options_ReferenceInterface::_,
                tubepress_lib_api_translation_TranslatorInterface::_
            ),
            'tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener' => array(
                tubepress_app_api_environment_EnvironmentInterface::_,
                tubepress_lib_api_translation_TranslatorInterface::_,
                tubepress_platform_api_util_StringUtilsInterface::_,
            ),
            'tubepress_app_impl_listeners_template_pre_PaginationListener' => array(
                tubepress_app_api_options_ContextInterface::_,
                tubepress_platform_api_url_UrlFactoryInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_,
                'tubepress_app_impl_theme_CurrentThemeService',
                tubepress_lib_api_translation_TranslatorInterface::_
            ),
            'tubepress_app_impl_listeners_template_pre_SearchInputListener' => array(
                tubepress_app_api_options_ContextInterface::_,
                tubepress_platform_api_url_UrlFactoryInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_
            ),
        );

        $servicesConsumers = array(
            'tubepress_app_impl_listeners_options_values_FeedOptions' => array(
                tubepress_app_api_media_MediaProviderInterface::__ => 'setMediaProviders',
            ),
            'tubepress_app_impl_listeners_media_CollectionListener' => array(
                tubepress_app_api_media_MediaProviderInterface::__ => 'setMediaProviders'
            ),
            'tubepress_app_impl_listeners_template_pre_MetaDisplayListener' => array(
                tubepress_app_api_media_MediaProviderInterface::__ => 'setMediaProviders'
            ),
            'tubepress_app_impl_listeners_embedded_EmbeddedListener' => array(
                'tubepress_app_api_embedded_EmbeddedProviderInterface' => 'setEmbeddedProviders',
            ),
            'tubepress_app_impl_listeners_player_PlayerListener' => array(
                'tubepress_app_api_player_PlayerLocationInterface' => 'setPlayerLocations',
            ),
            'tubepress_app_impl_listeners_search_SearchListener' => array(
                tubepress_app_api_media_MediaProviderInterface::__ => 'setMediaProviders',
            ),
            'tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener' => array(
                'tubepress_app_api_options_ui_FieldProviderInterface' => 'setFieldProviders',
                'tubepress_app_api_media_MediaProviderInterface'      => 'setMediaProviders',
            )
        );

        $listeners = array(

            /**
             * ADMIN GUI
             */
            tubepress_app_api_event_Events::HTML_SCRIPTS_ADMIN => array(
                100000 => array('tubepress_app_impl_listeners_admingui_BootstrapIe8Listener' => 'onAdminScripts')
            ),

            /**
             * GALLERY INIT JS
             */
            tubepress_app_api_event_Events::GALLERY_INIT_JS => array(
                100000 => array('tubepress_app_impl_listeners_gallery_GalleryListener'   => 'onGalleryInitJs'),
                98000  => array('tubepress_app_impl_listeners_embedded_EmbeddedListener' => 'onGalleryInitJs'),
                96000  => array('tubepress_app_impl_listeners_player_PlayerListener'     => 'onGalleryInitJs'),
            ),

            /**
             * HTML
             */
            tubepress_app_api_event_Events::HTML_GENERATION => array(
                100000 => array('tubepress_app_impl_listeners_search_SearchListener'              => 'onHtmlGenerationSearchInput',),
                98000  => array('tubepress_app_impl_listeners_html_generation_SoloPlayerListener' => 'onHtmlGeneration'),
                96000  => array('tubepress_app_impl_listeners_search_SearchListener'              => 'onHtmlGenerationSearchOutput',),
                94000  => array('tubepress_app_impl_listeners_html_generation_SingleItemListener' => 'onHtmlGeneration',),
                92000  => array('tubepress_app_impl_listeners_gallery_GalleryListener'            => 'onHtmlGeneration',)
            ),
            tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT => array(
                100000 => array('tubepress_app_impl_listeners_html_exception_LoggingListener' => 'onException',)
            ),
            tubepress_app_api_event_Events::HTML_GLOBAL_JS_CONFIG => array(
                100000 => array('tubepress_app_impl_listeners_html_jsconfig_BaseUrlSetter' => 'onGlobalJsConfig',)
            ),
            tubepress_app_api_event_Events::HTML_SCRIPTS => array(
                100000 => array('tubepress_app_api_html_HtmlGeneratorInterface' => 'onScripts')
            ),

            /**
             * HTTP
             */
            tubepress_app_api_event_Events::HTTP_AJAX . '.playerHtml' => array(
                100000 => array('tubepress_app_impl_listeners_http_ajax_PlayerAjaxCommand' => 'onAjax')
            ),
            tubepress_lib_api_http_Events::EVENT_HTTP_REQUEST => array(
                100000 => array('tubepress_app_impl_listeners_http_ApiCacheListener' => 'onRequest',)
            ),
            tubepress_lib_api_http_Events::EVENT_HTTP_RESPONSE => array(
                100000 => array('tubepress_app_impl_listeners_http_ApiCacheListener' => 'onResponse')
            ),

            /**
             * MEDIA
             */
            tubepress_app_api_event_Events::MEDIA_PAGE_NEW => array(
                100000 => array('tubepress_app_impl_listeners_media_PageListener'    => 'perPageSort'),
                98000  => array('tubepress_app_impl_listeners_media_PageListener'    => 'blacklist'),
                96000  => array('tubepress_app_impl_listeners_media_PageListener'    => 'capResults'),
                94000  => array('tubepress_app_impl_listeners_media_PageListener'    => 'prependItems'),
                93000  => array('tubepress_app_impl_listeners_media_PageListener'    => 'filterDuplicates'),
                92000  => array('tubepress_app_impl_listeners_player_PlayerListener' => 'onNewMediaPage'),
            ),
            tubepress_app_api_event_Events::MEDIA_PAGE_REQUEST => array(
                100000 => array('tubepress_app_impl_listeners_media_CollectionListener'  => 'onMediaPageRequest'),
                98000  => array('tubepress_app_impl_listeners_media_DispatchingListener' => 'onMediaPageRequest'),
            ),
            tubepress_app_api_event_Events::MEDIA_ITEM_REQUEST => array(
                100000 => array('tubepress_app_impl_listeners_media_CollectionListener'  => 'onMediaItemRequest'),
                98000  => array('tubepress_app_impl_listeners_media_DispatchingListener' => 'onMediaItemRequest'),
            ),

            /**
             * NVP
             */
            tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT => array(
                100000 => array('tubepress_app_impl_listeners_nvp_StringMagicListener' => 'onExternalInput'),
            ),

            /**
             * OPTIONS SET
             */
            tubepress_app_api_event_Events::OPTION_SET => array(
                200000  => array('tubepress_app_impl_listeners_options_set_BasicOptionValidity' => 'onOption'),
                -100000 => array('tubepress_app_impl_listeners_options_set_LoggingListener'     => 'onOptionSet')
            ),
            tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_app_api_options_Names::THEME => array(
                100000 => array('tubepress_app_impl_listeners_options_set_LegacyThemeListener' => 'onPreValidationSet')
            ),

            /**
             * OPTIONS VALUES
             */
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL => array(
                100000 => array('tubepress_app_impl_listeners_embedded_EmbeddedListener' => 'onAcceptableValues')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::SEARCH_PROVIDER => array(
                100000 => array('tubepress_app_impl_listeners_search_SearchListener' => 'onAcceptableValues')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_ORDER_BY => array(
                100000 => array('tubepress_app_impl_listeners_options_values_FeedOptions' => 'onOrderBy')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::GALLERY_SOURCE => array(
                100000 => array('tubepress_app_impl_listeners_options_values_FeedOptions' => 'onMode')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::THEME => array(
                100000 => array('tubepress_app_impl_listeners_options_values_ThemeListener' => 'onAcceptableValues')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::THEME_ADMIN => array(
                100000 => array('tubepress_app_impl_listeners_options_values_ThemeListener.admin' => 'onAcceptableValues')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::PLAYER_LOCATION => array(
                100000 => array('tubepress_app_impl_listeners_player_PlayerListener' => 'onAcceptableValues'),
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_PER_PAGE_SORT => array(
                100000 => array('tubepress_app_impl_listeners_options_values_PerPageSort' => 'onAcceptableValues')
            ),

            /**
             * TEMPLATE - SELECTION
             */
            tubepress_app_api_event_Events::TEMPLATE_SELECT . '.gallery/player/static' => array(
                100000 => array('tubepress_app_impl_listeners_player_PlayerListener' => 'onStaticPlayerTemplateSelection')
            ),
            tubepress_app_api_event_Events::TEMPLATE_SELECT . '.gallery/player/ajax' => array(
                100000 => array('tubepress_app_impl_listeners_player_PlayerListener' => 'onAjaxPlayerTemplateSelection')
            ),
            tubepress_app_api_event_Events::TEMPLATE_SELECT . '.single/embedded' => array(
                100000 => array('tubepress_app_impl_listeners_embedded_EmbeddedListener' => 'onEmbeddedTemplateSelect')
            ),

            /**
             * TEMPLATE - PRE
             */
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main' => array(
                100000 => array('tubepress_app_impl_listeners_gallery_GalleryListener'         => 'onGalleryTemplatePreRender'),
                98000 => array('tubepress_app_impl_listeners_template_pre_MetaDisplayListener' => 'onPreTemplate'),
                96000 => array('tubepress_app_impl_listeners_template_pre_PaginationListener'  => 'onGalleryTemplatePreRender'),
                94000 => array('tubepress_app_impl_listeners_player_PlayerListener'            => 'onGalleryTemplatePreRender')
            ),
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.single/main' => array(
                100000 => array('tubepress_app_impl_listeners_embedded_EmbeddedListener'        => 'onSingleItemTemplatePreRender'),
                98000 => array('tubepress_app_impl_listeners_template_pre_MetaDisplayListener' => 'onPreTemplate'),
            ),
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.search/input' => array(
                100000 => array('tubepress_app_impl_listeners_template_pre_SearchInputListener' => 'onSearchInputTemplatePreRender',)
            ),
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/ajax' => array(
                100000 => array('tubepress_app_impl_listeners_embedded_EmbeddedListener' => 'onPlayerTemplatePreRender'),
            ),
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/player/static' => array(
                100000 => array('tubepress_app_impl_listeners_embedded_EmbeddedListener' => 'onPlayerTemplatePreRender'),
            ),
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.options-ui/form' => array(
                100000 => array('tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener' => 'onOptionsGuiTemplate'),
            ),

            /**
             * TEMPLATE - POST
             */
            tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.gallery/main' => array(
                100000 => array('tubepress_app_impl_listeners_gallery_GalleryListener' => 'onPostGalleryTemplateRender'),
            ),
            tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.cssjs/styles' => array(
                100000 => array('tubepress_app_impl_listeners_template_post_CssJsPostListener' => 'onPostStylesTemplateRender'),
            ),
            tubepress_app_api_event_Events::TEMPLATE_POST_RENDER . '.cssjs/scripts' => array(
                100000 => array('tubepress_app_impl_listeners_template_post_CssJsPostListener' => 'onPostScriptsTemplateRender')
            ),
        );

        $containerBuilder->register(
            'tubepress_app_impl_listeners_options_values_ThemeListener.admin',
            'tubepress_app_impl_listeners_options_values_ThemeListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . '.admin'));

        foreach ($listenerData as $serviceId => $args) {

            $def = $containerBuilder->register($serviceId, $serviceId);

            foreach ($args as $argumentId) {

                $def->addArgument(new tubepress_platform_api_ioc_Reference($argumentId));
            }
        }

        foreach ($listeners as $eventName => $eventListeners) {
            foreach ($eventListeners as $priority => $listenerList) {
                foreach ($listenerList as $serviceId => $method) {

                    $def = $containerBuilder->getDefinition($serviceId);

                    if ($def === null) {

                        throw new LogicException("Cannot find definition for $serviceId");
                    }

                    $def->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(

                        'event'    => $eventName,
                        'method'   => $method,
                        'priority' => $priority
                    ));
                }
            }
        }

        foreach ($servicesConsumers as $serviceId => $consumptionData) {
            foreach ($consumptionData as $tag => $method) {

                $def = $containerBuilder->getDefinition($serviceId);

                $def->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                    'tag'    => $tag,
                    'method' => $method
                ));
            }
        }
    }

    private function _registerLogger(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_platform_api_log_LoggerInterface::_,
            'tubepress_app_impl_log_HtmlLogger'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_));
    }

    private function _registerMediaSingletons(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_app_api_media_AttributeFormatterInterface::_,
            'tubepress_app_impl_media_AttributeFormatter'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_util_TimeUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));

        $containerBuilder->register(
            tubepress_app_api_media_CollectorInterface::_,
            'tubepress_app_impl_media_Collector'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_));

        $containerBuilder->register(
            tubepress_app_api_media_HttpCollectorInterface::_,
            'tubepress_app_impl_media_HttpCollector'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_HttpClientInterface::_));
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__core',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR               => 20,
                tubepress_app_api_options_Names::CACHE_DIRECTORY                     => null,
                tubepress_app_api_options_Names::CACHE_ENABLED                       => true,
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS              => 21600, //six hours
                tubepress_app_api_options_Names::DEBUG_ON                            => true,
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY                   => false,
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT                     => 390,
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY                   => true,
                tubepress_app_api_options_Names::EMBEDDED_LOOP                       => false,
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL                => tubepress_app_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO                  => false,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH                      => 640,
                tubepress_app_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE      => null,
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST              => null,
                tubepress_app_api_options_Names::FEED_ORDER_BY                       => 'default',
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT                  => tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_NONE,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP               => 0,
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE               => 20,
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION             => false,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT                    => true,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS                => true,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS                   => false,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE              => true,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW              => true,
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS               => true,
                tubepress_app_api_options_Names::GALLERY_SOURCE                      => 'user',
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT                => 90,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH                 => 120,
                tubepress_app_api_options_Names::HTML_GALLERY_ID                     => null,
                tubepress_app_api_options_Names::HTML_HTTPS                          => false,
                tubepress_app_api_options_Names::HTML_OUTPUT                         => null,
                tubepress_app_api_options_Names::HTTP_METHOD                         => 'GET',
                tubepress_app_api_options_Names::META_DATEFORMAT                     => 'M j, Y',
                tubepress_app_api_options_Names::META_DESC_LIMIT                     => 80,
                tubepress_app_api_options_Names::META_DISPLAY_AUTHOR                 => false,
                tubepress_app_api_options_Names::META_DISPLAY_CATEGORY               => false,
                tubepress_app_api_options_Names::META_DISPLAY_DESCRIPTION            => false,
                tubepress_app_api_options_Names::META_DISPLAY_ID                     => false,
                tubepress_app_api_options_Names::META_DISPLAY_KEYWORDS               => false,
                tubepress_app_api_options_Names::META_DISPLAY_LENGTH                 => true,
                tubepress_app_api_options_Names::META_DISPLAY_TITLE                  => true,
                tubepress_app_api_options_Names::META_DISPLAY_UPLOADED               => false,
                tubepress_app_api_options_Names::META_DISPLAY_URL                    => false,
                tubepress_app_api_options_Names::META_DISPLAY_VIEWS                  => true,
                tubepress_app_api_options_Names::META_RELATIVE_DATES                 => false,
                tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => null,
                tubepress_app_api_options_Names::PLAYER_LOCATION                     => 'normal',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON                  => true,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION            => 0,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET              => 0,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS                   => true,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER                    => null,
                tubepress_app_api_options_Names::SEARCH_PROVIDER                     => 'youtube',
                tubepress_app_api_options_Names::SEARCH_RESULTS_ONLY                 => false,
                tubepress_app_api_options_Names::SEARCH_RESULTS_URL                  => null,
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD                   => 'tubepress',
                tubepress_app_api_options_Names::SINGLE_MEDIA_ITEM_ID                => null,
                tubepress_app_api_options_Names::SOURCES                             => null,
                tubepress_app_api_options_Names::THEME                               => 'tubepress/default',
                tubepress_app_api_options_Names::THEME_ADMIN                         => 'tubepress/admin-default',

            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR               => 'Cache cleaning factor',        //>(translatable)<
                tubepress_app_api_options_Names::CACHE_DIRECTORY                     => 'Cache directory',           //>(translatable)<
                tubepress_app_api_options_Names::CACHE_ENABLED                       => 'Enable API cache',                //>(translatable)<
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS              => 'Cache expiration time (seconds)', //>(translatable)<
                tubepress_app_api_options_Names::DEBUG_ON                            => 'Enable debugging',   //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY                   => 'Auto-play all videos',                               //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT                     => 'Max height (px)',                                    //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY                   => '"Lazy" play videos',                                 //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LOOP                       => 'Loop',                                               //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL                => 'Implementation',                                     //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO                  => 'Show title and rating before video starts',          //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_WIDTH                      => 'Max width (px)',                                     //>(translatable)<
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST              => 'Video blacklist',                    //>(translatable)<
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP               => 'Maximum total videos to retrieve',   //>(translatable)<
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE               => 'Thumbnails per page',                //>(translatable)<,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT                  => 'Per-page sort order',                //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION             => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_AUTONEXT                    => 'Play videos sequentially without user intervention', //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS                => 'Use "fluid" thumbnails',             //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS                   => 'Use high-quality thumbnails',        //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE              => 'Show pagination above thumbnails',   //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW              => 'Show pagination below thumbnails',   //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS               => 'Randomize thumbnail images',         //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT                => 'Height (px) of thumbs',              //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH                 => 'Width (px) of thumbs',               //>(translatable)<
                tubepress_app_api_options_Names::HTML_HTTPS                          => 'Enable HTTPS',       //>(translatable)<
                tubepress_app_api_options_Names::HTTP_METHOD                         => 'HTTP method',        //>(translatable)<
                tubepress_app_api_options_Names::META_DATEFORMAT                     => 'Date format',                //>(translatable)<
                tubepress_app_api_options_Names::META_DESC_LIMIT                     => 'Maximum description length', //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_AUTHOR                 => 'Author',           //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_CATEGORY               => 'Category',         //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_DESCRIPTION            => 'Description',      //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_ID                     => 'ID',               //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_KEYWORDS               => 'Keywords',         //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_LENGTH                 => 'Runtime',          //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_TITLE                  => 'Title',            //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_UPLOADED               => 'Date posted',      //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_URL                    => 'URL',              //>(translatable)<
                tubepress_app_api_options_Names::META_DISPLAY_VIEWS                  => 'View count',       //>(translatable)<
                tubepress_app_api_options_Names::META_RELATIVE_DATES                 => 'Use relative dates',         //>(translatable)<
                tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
                tubepress_app_api_options_Names::PLAYER_LOCATION                     => 'Play each video',      //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON                  => 'Scroll page to embedded player after thumbnail click',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION            => 'Scroll duration (ms)',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET              => 'Scroll offset (px)',
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS                   => 'Responsive embeds',    //>(translatable)<
                tubepress_app_api_options_Names::SEARCH_ONLY_USER                    => 'Restrict search results to videos from author', //>(translatable)<
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD                   => 'Shortcode keyword',  //>(translatable)<
                tubepress_app_api_options_Names::THEME                               => 'Theme',  //>(translatable)<

            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR       => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
                tubepress_app_api_options_Names::CACHE_DIRECTORY             => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
                tubepress_app_api_options_Names::CACHE_ENABLED               => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS      => 'Cache entries will be considered stale after the specified number of seconds. Default is 21600 (six hours).',   //>(translatable)<
                tubepress_app_api_options_Names::DEBUG_ON                    => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT             => sprintf('Default is %s.', 390), //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY           => 'Auto-play each video after thumbnail click.', //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_LOOP               => 'Continue playing the video until the user stops it.', //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL        => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_WIDTH              => sprintf('Default is %s.', 640), //>(translatable)<
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST      => 'A list of video IDs that should never be displayed.',                                          //>(translatable)<
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP       => 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', //>(translatable)<
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE       => sprintf('Default is %s. Maximum is %s.', 20, 50),                                               //>(translatable)<
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT          => 'Additional sort order applied to each individual page of a gallery',                           //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION     => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_AUTONEXT            => 'When a video finishes, this will start playing the next video in the gallery.',  //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS        => 'Dynamically set thumbnail spacing based on the width of their container.', //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS           => 'Note: this option cannot be used with the "randomize thumbnails" feature.', //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE      => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW      => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS       => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT        => sprintf('Default is %s.', 90),   //>(translatable)<
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH         => sprintf('Default is %s.', 120),  //>(translatable)<
                tubepress_app_api_options_Names::HTML_HTTPS                  => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
                tubepress_app_api_options_Names::HTTP_METHOD                 => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<
                tubepress_app_api_options_Names::META_DATEFORMAT             => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
                tubepress_app_api_options_Names::META_DESC_LIMIT             => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
                tubepress_app_api_options_Names::META_RELATIVE_DATES         => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON          => 'Only applies when the video player is already embedded on the page; i.e. does not apply to modal or popup players.',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION    => 'Set to 0 for "instant" scroll.',
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET      => 'Set to 0 to scroll to the top of the embedded player. Negative or positive values here will scroll to above or below the player, respectively.',
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS           => 'Auto-resize media players to best fit the viewer\'s screen.', //>(translatable)<
                tubepress_app_api_options_Names::SEARCH_ONLY_USER            => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD           => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<,

            ),
        ))->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_NO_PERSIST => array(
                tubepress_app_api_options_Names::HTML_GALLERY_ID,
                tubepress_app_api_options_Names::HTML_OUTPUT,
                tubepress_app_api_options_Names::SINGLE_MEDIA_ITEM_ID,
                tubepress_app_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE,
            ),

            tubepress_app_api_options_Reference::PROPERTY_PRO_ONLY => array(
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
                tubepress_app_api_options_Names::SOURCES,
            ),
        ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS => array(
                tubepress_app_api_options_Names::SEARCH_ONLY_USER
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_app_api_options_Names::HTML_GALLERY_ID,
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_app_api_options_Names::META_DESC_LIMIT,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER => array(
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_app_api_listeners_options_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_app_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOption',
                ));
            }
        }

        $fixedValuesMap = array(
            tubepress_app_api_options_Names::HTTP_METHOD => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            ),
            tubepress_app_api_options_Names::FEED_PER_PAGE_SORT => array(
                tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_NONE   => 'none',           //>(translatable)<
                tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM => 'random',         //>(translatable)<
            )
        );
        foreach ($fixedValuesMap as $optionName => $valuesMap) {
            $containerBuilder->register(
                'fixed_values.' . $optionName,
                'tubepress_app_api_listeners_options_FixedValuesListener'
            )->addArgument($valuesMap)
             ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'priority' => 100000,
                'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                'method'   => 'onAcceptableValues'
            ));
        }
    }

    private function _registerOptionsSingletons(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_app_api_options_AcceptableValuesInterface::_,
            'tubepress_app_impl_options_AcceptableValues'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_));

        $containerBuilder->register(
            tubepress_app_api_options_ContextInterface::_,
            'tubepress_app_impl_options_Context'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_));

        $containerBuilder->register(
            tubepress_app_api_options_ReferenceInterface::_,
            'tubepress_app_impl_options_DispatchingReference'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_api_options_ReferenceInterface::_,
            'method' => 'setReferences'
        ));

        $containerBuilder->register(
            tubepress_app_api_options_PersistenceInterface::_,
            'tubepress_app_impl_options_Persistence'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceBackendInterface::_));
    }

    private function _registerOptionsUiSingletons(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_app_api_options_ui_FieldBuilderInterface::_,
            'tubepress_app_impl_options_ui_FieldBuilder'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_api_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders'
        ));

        $containerBuilder->register(
            'tubepress_app_impl_html_CssAndJsGenerationHelper.admin',
            'tubepress_app_impl_html_CssAndJsGenerationHelper'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_impl_theme_CurrentThemeService.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addArgument(tubepress_app_api_event_Events::HTML_STYLESHEETS_ADMIN)
         ->addArgument(tubepress_app_api_event_Events::HTML_SCRIPTS_ADMIN)
         ->addArgument('options-ui/cssjs/styles')
         ->addArgument('options-ui/cssjs/scripts');

        $containerBuilder->register(
            tubepress_app_api_options_ui_FormInterface::_,
            'tubepress_app_impl_options_ui_Form'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_impl_html_CssAndJsGenerationHelper.admin'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_app_api_options_ui_FieldProviderInterface',
            'method' => 'setFieldProviders',
        ));
    }

    private function _registerPlayers(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_impl_player_JsPlayerLocation__jqmodal',
            'tubepress_app_impl_player_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_JQMODAL)
         ->addArgument('with jqModal')                                          //>(translatable)<)
         ->addArgument('gallery/players/jqmodal/static')
         ->addArgument('gallery/players/jqmodal/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_app_impl_player_JsPlayerLocation__normal',
            'tubepress_app_impl_player_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_NORMAL)
         ->addArgument('normally (at the top of your gallery)')                 //>(translatable)<
         ->addArgument('gallery/players/normal/static')
         ->addArgument('gallery/players/normal/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_app_impl_player_JsPlayerLocation__popup',
            'tubepress_app_impl_player_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_POPUP)
         ->addArgument('in a popup window')                 //>(translatable)<
         ->addArgument('gallery/players/popup/static')
         ->addArgument('gallery/players/popup/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_app_impl_player_JsPlayerLocation__shadowbox',
            'tubepress_app_impl_player_JsPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX)
         ->addArgument('with Shadowbox')                 //>(translatable)<
         ->addArgument('gallery/players/shadowbox/static')
         ->addArgument('gallery/players/shadowbox/ajax')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_app_impl_player_SoloOrStaticPlayerLocation__solo',
            'tubepress_app_impl_player_SoloOrStaticPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SOLO)
         ->addArgument('in a new window on its own')                 //>(translatable)<
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');

        $containerBuilder->register(
            'tubepress_app_impl_player_SoloOrStaticPlayerLocation__static',
            'tubepress_app_impl_player_SoloOrStaticPlayerLocation'
        )->addArgument(tubepress_app_api_options_AcceptableValues::PLAYER_LOC_STATIC)
         ->addArgument('statically (page refreshes on each thumbnail click)')                 //>(translatable)<
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->addArgument('gallery/players/static/static')
         ->addTag('tubepress_app_api_player_PlayerLocationInterface');
    }

    private function _registerOptionsUiFieldProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::CACHE_ENABLED,
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_app_api_options_Names::EMBEDDED_LOOP,
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT,
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::DEBUG_ON,
                tubepress_app_api_options_Names::META_RELATIVE_DATES,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
            ),
            'dropdown' => array(
                tubepress_app_api_options_Names::PLAYER_LOCATION,
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
                tubepress_app_api_options_Names::HTTP_METHOD,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
            ),
            'text' => array(
                tubepress_app_api_options_Names::CACHE_DIRECTORY,
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR,
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
                tubepress_app_api_options_Names::META_DATEFORMAT,
                tubepress_app_api_options_Names::META_DESC_LIMIT,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST,
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER,
            ),
            'fieldProviderFilter' => array(
                tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField::FIELD_ID
            ),
            'metaMultiSelect' => array(
                'does not matter'
            ),
            'orderBy' => array(
                tubepress_app_api_options_Names::FEED_ORDER_BY
            ),
            'theme' => array(
                tubepress_app_api_options_Names::THEME
            ),
            'gallerySource' => array(
                tubepress_app_api_options_Names::GALLERY_SOURCE,
            ),
            'multiSourceText' => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'core_field_' . $id;

                $containerBuilder->register(
                    $serviceId,
                    'tubepress_app_api_options_ui_FieldInterface'
                )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);

                $fieldReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories = array(
            array(tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE, 'Which videos?'), //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::THUMBNAILS,     'Thumbnails'),    //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::EMBEDDED,       'Player'),        //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::META,           'Meta'),          //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::FEED,           'Feed'),          //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::CACHE,          'Cache'),         //>(translatable)<,
            array(tubepress_app_api_options_ui_CategoryNames::ADVANCED,       'Advanced'),      //>(translatable)<
            array(tubepress_app_api_options_ui_CategoryNames::THEME,          'Theme'),          //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'core_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_app_impl_options_ui_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
             ->addArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(
                tubepress_app_api_options_Names::GALLERY_SOURCE,
            ),
            tubepress_app_api_options_ui_CategoryNames::CACHE => array(
                tubepress_app_api_options_Names::CACHE_ENABLED,
                tubepress_app_api_options_Names::CACHE_DIRECTORY,
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR,
            ),
            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_app_api_options_Names::PLAYER_LOCATION,
                tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
                tubepress_app_api_options_Names::EMBEDDED_HEIGHT,
                tubepress_app_api_options_Names::EMBEDDED_WIDTH,
                tubepress_app_api_options_Names::RESPONSIVE_EMBEDS,
                tubepress_app_api_options_Names::EMBEDDED_LAZYPLAY,
                tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO,
                tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY,
                tubepress_app_api_options_Names::EMBEDDED_LOOP,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_ON,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_DURATION,
                tubepress_app_api_options_Names::EMBEDDED_SCROLL_OFFSET,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT
            ),
            tubepress_app_api_options_ui_CategoryNames::THUMBNAILS => array(
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS
            ),
            tubepress_app_api_options_ui_CategoryNames::ADVANCED => array(
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::HTTP_METHOD,
                tubepress_app_api_options_Names::DEBUG_ON
            ),
            tubepress_app_api_options_ui_CategoryNames::META => array(
                tubepress_app_impl_options_ui_fields_templated_multi_MetaMultiSelectField::FIELD_ID,
                tubepress_app_api_options_Names::META_DATEFORMAT,
                tubepress_app_api_options_Names::META_RELATIVE_DATES,
                tubepress_app_api_options_Names::META_DESC_LIMIT,
            ),
            tubepress_app_api_options_ui_CategoryNames::FEED => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::FEED_ORDER_BY,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
                tubepress_app_api_options_Names::SEARCH_ONLY_USER,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST,
            ),
            tubepress_app_api_options_ui_CategoryNames::THEME => array(
                tubepress_app_api_options_Names::THEME
            )
        );

        $containerBuilder->register(
            'tubepress_app_impl_options_ui_FieldProvider',
            'tubepress_app_impl_options_ui_FieldProvider'
        )->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    private function _registerShortcode(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_app_api_shortcode_ParserInterface::_,
            'tubepress_app_impl_shortcode_Parser'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));
    }

    private function _registerTheme(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_platform_impl_boot_helper_uncached_Serializer',
            'tubepress_platform_impl_boot_helper_uncached_Serializer'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_));

        $parallelServices = array(
            array('',       '',       tubepress_app_api_options_Names::THEME),
            array('.admin', 'admin-', tubepress_app_api_options_Names::THEME_ADMIN),
        );

        foreach ($parallelServices as $serviceInfo) {

            $serviceSuffix  = $serviceInfo[0];
            $artifactPrefix = $serviceInfo[1];
            $optionName     = $serviceInfo[2];

            $containerBuilder->register(
                tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . $serviceSuffix,
                'tubepress_platform_impl_boot_helper_uncached_contrib_SerializedRegistry'
            )->addArgument(sprintf('%%%s%%', tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS))
             ->addArgument($artifactPrefix . 'themes')
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_platform_impl_boot_helper_uncached_Serializer'));

            $containerBuilder->register(
                'tubepress_app_impl_theme_CurrentThemeService' . $serviceSuffix,
                'tubepress_app_impl_theme_CurrentThemeService'
            )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . $serviceSuffix))
             ->addArgument('tubepress/' . $artifactPrefix . 'default')
             ->addArgument($optionName);
        }
    }

    private function _registerVendorServices(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );

        $containerBuilder->register(
            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );

        $containerBuilder->register(

            'tubepress_app_impl_vendor_stash_FilesystemCacheBuilder',
            'tubepress_app_impl_vendor_stash_FilesystemCacheBuilder'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_));

        $containerBuilder->register(

            'ehough_stash_interfaces_PoolInterface',
            'ehough_stash_Pool'
        )->addMethodCall('setDriver', array(new tubepress_platform_api_ioc_Reference('ehough_stash_interfaces_DriverInterface')));

        $containerBuilder->register(
            'ehough_stash_interfaces_DriverInterface',
            'ehough_stash_interfaces_DriverInterface'
        )->setFactoryService('tubepress_app_impl_vendor_stash_FilesystemCacheBuilder')
         ->setFactoryMethod('buildFilesystemDriver');
    }
}
