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
 * Adds shortcode handlers to TubePress.
 */
class tubepress_addons_core_impl_ioc_IocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder A ContainerBuilder instance
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public final function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Singleton services.
         */
        $this->_registerEnvironmentDetector($containerBuilder);
        $this->_registerEventDispatcher($containerBuilder);
        $this->_registerFilesystemFinderFactory($containerBuilder);
        $this->_registerAjaxHandler($containerBuilder);
        $this->_registerCacheService($containerBuilder);
        $this->_registerCssAndJsGenerator($containerBuilder);
        $this->_registerCssAndJsRegistry($containerBuilder);
        $this->_registerEmbeddedHtmlGenerator($containerBuilder);
        $this->_registerExecutionContext($containerBuilder);
        $this->_registerFeedFetcher($containerBuilder);
        $this->_registerFilesystem($containerBuilder);
        $this->_registerHttpClient($containerBuilder);
        $this->_registerHttpRequestParameterService($containerBuilder);
        $this->_registerHttpResponseCodeHandler($containerBuilder);
        $this->_registerAggregateOptionProvider($containerBuilder);
        $this->_registerCoreOptionProvider($containerBuilder);
        $this->_registerMetaOptionNameService($containerBuilder);
        $this->_registerPlayerHtmlGenerator($containerBuilder);
        $this->_registerQueryStringService($containerBuilder);
        $this->_registerShortcodeHtmlGenerator($containerBuilder);
        $this->_registerShortcodeParser($containerBuilder);
        $this->_registerTemplateBuilder($containerBuilder);
        $this->_registerThemeHandler($containerBuilder);
        $this->_registerVideoCollector($containerBuilder);

        /**
         * Pluggable services.
         */
        $this->_registerPluggableServices($containerBuilder);

        /**
         * Listeners
         */
        $this->_registerListeners($containerBuilder);
    }

    private function _registerAjaxHandler(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_http_AjaxHandler::_,
            'tubepress_impl_http_DefaultAjaxHandler'
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_http_PluggableAjaxCommandService::_, 'method' => 'setPluggableAjaxCommandHandlers'));
    }

    private function _registerCacheService(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Long, guaranteed unique service IDs (since they're anonymous)
         */
        $actualPoolServiceId = 'tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_actualPoolServiceId';
        $builderServiceId    = 'tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_builderServiceId';

        /**
         * First register the default cache builder.
         */
        $containerBuilder->register(

            $builderServiceId,
            'tubepress_addons_core_impl_ioc_FilesystemCacheBuilder'
        );

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $containerBuilder->setDefinition($actualPoolServiceId, $actualPoolDefinition);

        $containerBuilder->register(

            'ehough_stash_interfaces_PoolInterface',
            'tubepress_impl_cache_PoolDecorator'

        )->addArgument(new tubepress_impl_ioc_Reference($actualPoolServiceId));
    }

    private function _registerCssAndJsRegistry(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_html_CssAndJsRegistryInterface::_,
            'tubepress_impl_html_CssAndJsRegistry'
        );
    }

    private function _registerCssAndJsGenerator(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_html_CssAndJsHtmlGeneratorInterface::_,
            'tubepress_impl_html_CssAndJsHtmlGenerator'
        );
    }

    private function _registerEmbeddedHtmlGenerator(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'))
            ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'));
    }

    private function _registerExecutionContext(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_context_ExecutionContext::_,
            'tubepress_impl_context_MemoryExecutionContext'
        );
    }

    private function _registerFeedFetcher(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_feed_FeedFetcher::_,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );
    }

    private function _registerFilesystem(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );
    }

    private function _registerHttpClient(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerHttpMessageParser($containerBuilder);
        $this->_registerHttpTransportChain($containerBuilder);
        $this->_registerHttpContentDecoder($containerBuilder);
        $this->_registerHttpTransferDecoder($containerBuilder);
        $this->_registerHttpRequestDefaultHeadersListener($containerBuilder);
        $this->_registerHttpRequestLoggingListener($containerBuilder);
        $this->_registerHttpTransferDecodingListener($containerBuilder);
        $this->_registerHttpContentDecodingListener($containerBuilder);
        $this->_registerHttpResponseLoggingListener($containerBuilder);

        $containerBuilder->register(

            'ehough_shortstop_api_HttpClientInterface',
            'ehough_shortstop_impl_DefaultHttpClient'

        )->addArgument(new tubepress_impl_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'))
            ->addArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_impl_DefaultHttpClient_transportchain'));
    }

    private function _registerHttpRequestParameterService(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );
    }

    private function _registerHttpResponseCodeHandler(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler'
        );
    }

    private function _registerAggregateOptionProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_options_OptionProvider::_,
            'tubepress_impl_options_OptionProviderAggregate'
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_OptionProvider::_, 'method' => 'setAddonOptionProviders'))
         ->addMethodCall('setRegisteredOptionNames', array('%tubePressOptionNames%'));
    }

    private function _registerCoreOptionProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_addons_core_impl_options_CoreOptionProvider',
            'tubepress_addons_core_impl_options_CoreOptionProvider'
        )->addTag(tubepress_spi_options_OptionProvider::_)
            ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'))
            ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'))
            ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));
    }

    private function _registerMetaOptionNameService(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_addons_core_impl_options_MetaOptionNameService::_,
            tubepress_addons_core_impl_options_MetaOptionNameService::_
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setVideoProviders'));
    }

    private function _registerPlayerHtmlGenerator(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'));
    }

    private function _registerQueryStringService(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );
    }

    private function _registerShortcodeHtmlGenerator(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_shortcode_PluggableShortcodeHandlerService::_, 'method' => 'setPluggableShortcodeHandlers'));
    }

    private function _registerShortcodeParser(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );
    }

    private function _registerTemplateBuilder(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeHandler(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler'
        )->addArgument('%themes%');
    }

    private function _registerVideoCollector(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector'
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));
    }

    private function _registerPluggableServices(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerOptionsPageParticipant($containerBuilder);

        $this->_registerAjaxHandlers($containerBuilder);

        $this->_registerPlayerLocations($containerBuilder);

        $this->_registerShortcodeHandlers($containerBuilder);
    }

    private function _registerOptionsPageParticipant(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $categoryMap = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_GALLERYSOURCE => 'Which videos?',  //>(translatable)<
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_THUMBS        => 'Thumbnails',     //>(translatable)<
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER        => 'Player',         //>(translatable)<
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_META          => 'Meta',           //>(translatable)<
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_FEED          => 'Feed',           //>(translatable)<
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_CACHE         => 'Cache',          //>(translatable)<
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_ADVANCED      => 'Advanced',       //>(translatable)<
        );

        $categoryIndex = 0;

        foreach ($categoryMap as $id => $displayName) {

            $containerBuilder->register(

                'core_options_category_' . $categoryIndex++,
                'tubepress_impl_options_ui_OptionsPageItem'
            )->addArgument($id)
                ->addArgument($displayName);
        }

        $categoryReferences = array();

        for ($x = 0 ; $x < $categoryIndex; $x++) {

            $categoryReferences[] = new tubepress_impl_ioc_Reference('core_options_category_' . $x);
        }

        $fieldIndex = 0;

        //Gallery source field
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_GallerySourceField'
        );

        //Filter field
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_ParticipantFilterField'
        );

        //Meta multi-select
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField'
        );

        //Theme field
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_ThemeField'
        );

        $fieldMap = array(

            //Thumbnail fields
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT     => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH      => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION  => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS     => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE   => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW   => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Thumbs::HQ_THUMBS        => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS    => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 'tubepress_impl_options_ui_fields_TextField',

            //Player fields
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_api_const_options_names_Embedded::PLAYER_IMPL     => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH  => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Embedded::LAZYPLAY        => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Embedded::SHOW_INFO       => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Embedded::AUTONEXT        => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Embedded::AUTOPLAY        => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Embedded::LOOP            => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Embedded::ENABLE_JS_API   => 'tubepress_impl_options_ui_fields_BooleanField',

            //Meta fields
            tubepress_api_const_options_names_Meta::DATEFORMAT     => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Meta::RELATIVE_DATES => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Meta::DESC_LIMIT     => 'tubepress_impl_options_ui_fields_TextField',

            //Feed fields
            tubepress_api_const_options_names_Feed::ORDER_BY         => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_api_const_options_names_Feed::PER_PAGE_SORT    => 'tubepress_impl_options_ui_fields_DropdownField',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST  => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'tubepress_impl_options_ui_fields_TextField',

            //Cache fields
            tubepress_api_const_options_names_Cache::CACHE_ENABLED          => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Cache::CACHE_DIR              => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS => 'tubepress_impl_options_ui_fields_TextField',
            tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR     => 'tubepress_impl_options_ui_fields_TextField',

            //Advanced fields
            tubepress_api_const_options_names_Advanced::DEBUG_ON    => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Advanced::HTTPS       => 'tubepress_impl_options_ui_fields_BooleanField',
            tubepress_api_const_options_names_Advanced::HTTP_METHOD => 'tubepress_impl_options_ui_fields_DropdownField',
        );

        foreach ($fieldMap as $id => $class) {

            $containerBuilder->register('core_options_field_' . $fieldIndex++, $class)->addArgument($id);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('core_options_field_' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_THUMBS => array(

                tubepress_api_const_options_names_Thumbs::THEME,
                tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT,
                tubepress_api_const_options_names_Thumbs::THUMB_WIDTH,
                tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION,
                tubepress_api_const_options_names_Thumbs::FLUID_THUMBS,
                tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE,
                tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW,
                tubepress_api_const_options_names_Thumbs::HQ_THUMBS,
                tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS,
                tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_api_const_options_names_Embedded::PLAYER_LOCATION,
                tubepress_api_const_options_names_Embedded::PLAYER_IMPL,
                tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT,
                tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH,
                tubepress_api_const_options_names_Embedded::LAZYPLAY,
                tubepress_api_const_options_names_Embedded::SHOW_INFO,
                tubepress_api_const_options_names_Embedded::AUTONEXT,
                tubepress_api_const_options_names_Embedded::AUTOPLAY,
                tubepress_api_const_options_names_Embedded::LOOP,
                tubepress_api_const_options_names_Embedded::ENABLE_JS_API,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_META => array(

                tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField::FIELD_ID,
                tubepress_api_const_options_names_Meta::DATEFORMAT,
                tubepress_api_const_options_names_Meta::RELATIVE_DATES,
                tubepress_api_const_options_names_Meta::DESC_LIMIT,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_FEED => array(

                tubepress_api_const_options_names_Feed::ORDER_BY,
                tubepress_api_const_options_names_Feed::PER_PAGE_SORT,
                tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP,
                tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST,
                tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_CACHE => array(

                tubepress_api_const_options_names_Cache::CACHE_ENABLED,
                tubepress_api_const_options_names_Cache::CACHE_DIR,
                tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS,
                tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR,
            ),

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_ADVANCED => array(

                tubepress_api_const_options_names_Advanced::DEBUG_ON,
                tubepress_api_const_options_names_Advanced::HTTPS,
                tubepress_api_const_options_names_Advanced::HTTP_METHOD
            )
        );

        $containerBuilder->register(

            'core_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->addArgument('core')
            ->addArgument('Core')  //this will never be shown, so don't translate
            ->addArgument($categoryReferences)
            ->addArgument($fieldReferences)
            ->addArgument($map)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _registerAjaxHandlers(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService',
            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService'

        )->addTag(tubepress_spi_http_PluggableAjaxCommandService::_);
    }

    private function _registerPlayerLocations(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $playerLocationClasses = array(

            'tubepress_addons_core_impl_player_JqModalPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_NormalPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_PopupPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_StaticPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_VimeoPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_SoloPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_YouTubePluggablePlayerLocationService',
        );

        foreach ($playerLocationClasses as $playerLocationClass) {

            $containerBuilder->register(

                $playerLocationClass, $playerLocationClass

            )->addTag(tubepress_spi_player_PluggablePlayerLocationService::_);
        }
    }

    private function _registerShortcodeHandlers(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'))
            ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'))
            ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'
        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $listeners = array(

            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED, 'method' => 'onEmbeddedTemplate', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION, 'method' => 'onPlayerTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT, 'method' => 'onSearchInputTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY, 'method' => 'onGalleryHtml', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10400),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10300),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10200),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10300),

            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10200),

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_html_JsConfig' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, 'method' => 'onPreScriptsHtml', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_html_PreCssHtmlListener' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_STYLESHEETS_PRE, 'method' => 'onBeforeCssHtml', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter' =>
                array('event' => tubepress_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG, 'method' => 'onJsConfig', 'priority' => 10000),
        );

        foreach ($listeners as $className => $tagAttributes) {

            $containerBuilder->register($className, $className)->addTag(self::TAG_EVENT_LISTENER, $tagAttributes);
        }

        $containerBuilder->register(
            'tubepress_addons_core_impl_listeners_StringMagicFilter_preValidation',
            'tubepress_addons_core_impl_listeners_StringMagicFilter'
        )->addTag(self::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET,
                'method'   => 'magic',
                'priority' => 10100
        ));

        $containerBuilder->register(
            'tubepress_addons_core_impl_listeners_StringMagicFilter_readFromExternal',
            'tubepress_addons_core_impl_listeners_StringMagicFilter'
        )->addTag(self::TAG_EVENT_LISTENER,  array(
                'event'    => tubepress_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT,
                'method'   => 'magic',
                'priority' => 10000));

        $containerBuilder->register(
            'tubepress_addons_core_impl_listeners_options_LegacyThemeListener',
            'tubepress_addons_core_impl_listeners_options_LegacyThemeListener'
        )->addTag(self::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_api_const_options_names_Thumbs::THEME,
                'method'   => 'onPreValidationSet',
                'priority' => 300000
        ));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams',
            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams'
        )->addTag(self::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT,
                'method'   => 'onGalleryInitJs',
                'priority' => 10000)
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_player_PluggablePlayerLocationService::_,
                'method' => 'setPluggablePlayerLocations'
        ));
    }

    private function _registerHttpTransportChain(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Register the transport commands and chain.
         */
        $transportClasses = array(

            'ehough_shortstop_impl_exec_command_ExtCommand',
            'ehough_shortstop_impl_exec_command_CurlCommand',
            'ehough_shortstop_impl_exec_command_StreamsCommand',
            'ehough_shortstop_impl_exec_command_FsockOpenCommand',
            'ehough_shortstop_impl_exec_command_FopenCommand'
        );

        $transportReferences = array();

        foreach ($transportClasses as $transportClass) {

            $containerBuilder->register($transportClass, $transportClass)
                ->addArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpMessageParser'))
                ->addArgument(new tubepress_impl_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));

            array_push($transportReferences, new tubepress_impl_ioc_Reference($transportClass));
        }

        $transportChainId = 'ehough_shortstop_impl_DefaultHttpClient_transportchain';

        tubepress_impl_ioc_ChainRegistrar::registerChainDefinitionByReferences($containerBuilder, $transportChainId, $transportReferences);
    }

    private function _registerHttpMessageParser(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_shortstop_spi_HttpMessageParser',
            'ehough_shortstop_impl_exec_DefaultHttpMessageParser'
        );
    }

    private function _registerHttpResponseLoggingListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_shortstop_impl_listeners_response_ResponseLoggingListener',
            'ehough_shortstop_impl_listeners_response_ResponseLoggingListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10100));
    }

    private function _registerHttpContentDecodingListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener__content',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'))
            ->addArgument('Content')
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10200));
    }

    private function _registerHttpTransferDecodingListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener__transfer',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpTransferDecoder'))
            ->addArgument('Transfer')
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10300));
    }

    private function _registerHttpRequestLoggingListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_shortstop_impl_listeners_request_RequestLoggingListener',
            'ehough_shortstop_impl_listeners_request_RequestLoggingListener'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onPreRequest', 'priority' => 5000));
    }

    private function _registerHttpRequestDefaultHeadersListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener',
            'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onPreRequest', 'priority' => 10000));
    }

    private function _registerHttpContentDecoder(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Register the content decoding commands and chain.
         */
        $contentDecoderCommands = array(

            'ehough_shortstop_impl_decoding_content_command_NativeGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_SimulatedGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1951DecompressingCommand',
        );

        $contentDecoderChainId = 'ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain';

        tubepress_impl_ioc_ChainRegistrar::registerChainDefinitionByClassNames($containerBuilder, $contentDecoderChainId, $contentDecoderCommands);

        /**
         * Register the content decoder.
         */
        $containerBuilder->register(

            'ehough_shortstop_spi_HttpContentDecoder',
            'ehough_shortstop_impl_decoding_content_HttpContentDecodingChain'

        )->addArgument(new tubepress_impl_ioc_Reference($contentDecoderChainId));
    }

    private function _registerHttpTransferDecoder(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Register the transfer decoding command and chain.
         */
        $transferDecoderCommands = array(

            'ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand'
        );

        $transferDecoderChainId = 'ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain';

        tubepress_impl_ioc_ChainRegistrar::registerChainDefinitionByClassNames($containerBuilder, $transferDecoderChainId, $transferDecoderCommands);

        /**
         * Register the transfer decoder.
         */
        $containerBuilder->register(

            'ehough_shortstop_spi_HttpTransferDecoder',
            'ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain'

        )->addArgument(new tubepress_impl_ioc_Reference($transferDecoderChainId));
    }

    private function _registerEnvironmentDetector(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_environment_EnvironmentDetector::_,
            'tubepress_impl_environment_SimpleEnvironmentDetector'
        );
    }

    private function _registerFilesystemFinderFactory(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }

    private function _registerEventDispatcher(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_tickertape_ContainerAwareEventDispatcher',
            'ehough_tickertape_ContainerAwareEventDispatcher'
        )->addArgument(new tubepress_impl_ioc_Reference('service_container'));

        $containerBuilder->register(

            tubepress_api_event_EventDispatcherInterface::_,
            'tubepress_impl_event_DefaultEventDispatcher'
        )->addArgument(new tubepress_impl_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));
    }
}