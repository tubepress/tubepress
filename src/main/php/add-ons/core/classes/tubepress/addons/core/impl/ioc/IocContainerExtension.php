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
 * Primary services for TubePress.
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
        $this->_registerFilesystemFinderFactory($containerBuilder);
        $this->_registerAjaxHandler($containerBuilder);
        $this->_registerCacheService($containerBuilder);
        $this->_registerEmbeddedHtmlGenerator($containerBuilder);
        $this->_registerFilesystem($containerBuilder);
        $this->_registerHttpRequestParameterService($containerBuilder);
        $this->_registerHttpResponseCodeHandler($containerBuilder);
        $this->_registerCoreOptionProvider($containerBuilder);
        $this->_registerMetaOptionNameService($containerBuilder);
        $this->_registerPlayerHtmlGenerator($containerBuilder);
        $this->_registerTemplateBuilder($containerBuilder);
        $this->_registerThemeHandler($containerBuilder);
        $this->_registerThemeFinder($containerBuilder);
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
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $containerBuilder->setDefinition($actualPoolServiceId, $actualPoolDefinition);

        $containerBuilder->register(

            'ehough_stash_interfaces_PoolInterface',
            'tubepress_impl_cache_PoolDecorator'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_impl_ioc_Reference($actualPoolServiceId));
    }

    private function _registerEmbeddedHtmlGenerator(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'))
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'));
    }

    private function _registerFilesystem(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );
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


    private function _registerCoreOptionProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_addons_core_impl_options_CoreOptionProvider',
            'tubepress_addons_core_impl_options_CoreOptionProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->addTag(tubepress_api_options_ProviderInterface::_)
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
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'));
    }




    private function _registerTemplateBuilder(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeFinder(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_theme_ThemeFinderInterface::_,
            'tubepress_impl_theme_ThemeFinder'
        )->addArgument(new ehough_iconic_Reference('ehough_finder_FinderFactoryInterface'))
         ->addArgument(new ehough_iconic_Reference(tubepress_api_environment_EnvironmentInterface::_));
    }

    private function _registerThemeHandler(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_theme_ThemeHandlerInterface::_,
            'tubepress_impl_theme_ThemeHandler'
        )->addArgument('%themes%')
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_));
    }

    private function _registerVideoCollector(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));
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
            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_THEME         => 'Theme',          //>(translatable)<
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
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_));

        //Filter field
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_ParticipantFilterField'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        //Meta multi-select
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        //Theme field
        $containerBuilder->register(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_ThemeField'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

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

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_THEME => array(

                tubepress_api_const_options_names_Thumbs::THEME,
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
            ->addArgument(false)
            ->addArgument(false)
            ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _registerAjaxHandlers(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService',
            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addTag(tubepress_spi_http_PluggableAjaxCommandService::_);
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

            )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
             ->addTag(tubepress_spi_player_PluggablePlayerLocationService::_);
        }
    }

    private function _registerShortcodeHandlers(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'))
            ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $containerBuilder->register(

            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_CurrentUrlServiceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT, 'method' => 'onSearchInputTemplate', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_CurrentUrlServiceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10200));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter',
            'tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG, 'method' => 'onJsConfig', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED, 'method' => 'onEmbeddedTemplate', 'priority' => 10100));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta',
            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ProviderInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ProviderInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs',
            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY, 'method' => 'onGalleryHtml', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION, 'method' => 'onPlayerTemplate', 'priority' => 10000));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10100));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10400));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10300));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10100));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10300));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10100));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10200));

        $containerBuilder->register(

            'tubepress_addons_core_impl_listeners_html_JsConfig',
            'tubepress_addons_core_impl_listeners_html_JsConfig'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, 'method' => 'onPreScriptsHtml', 'priority' => 10000));

        $listeners = array(


            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_html_PreCssHtmlListener' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_STYLESHEETS_PRE, 'method' => 'onBeforeCssHtml', 'priority' => 10000),
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
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ProviderInterface::_))
         ->addTag(self::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT,
                'method'   => 'onGalleryInitJs',
                'priority' => 10000)
        )->addTag(self::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_player_PluggablePlayerLocationService::_,
                'method' => 'setPluggablePlayerLocations'
        ));
    }


    private function _registerFilesystemFinderFactory(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }
}