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
 * @covers tubepress_addons_core_impl_ioc_IocContainerExtension<extended>
 */
class tubepress_test_addons_core_impl_ioc_IocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function prepareForLoad()
    {
        $this->_filesystemFinderFactory();
        $this->_ajaxHandler();
        $this->_cacheService();
        $this->_embeddedGenerator();
        $this->_filesystem();
        $this->_hrps();
        $this->_hrch();
        $this->_optionProvider();
        $this->_optionMetaNameService();
        $this->_registerPlayerHtml();
        $this->_shortcodeParser();
        $this->_templateBuilder();
        $this->_themeHandler();
        $this->_themeFinder();
        $this->_videoCollector();

        $this->_pluggables();

        $this->_listeners();
    }

    protected function buildSut()
    {
        return new tubepress_addons_core_impl_ioc_IocContainerExtension();
    }

    private function _listeners()
    {
        $this->expectRegistration(
            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_CurrentUrlServiceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT, 'method' => 'onSearchInputTemplate', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_CurrentUrlServiceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
        ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10200));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter',
            'tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_GLOBAL_JS_CONFIG, 'method' => 'onJsConfig', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED, 'method' => 'onEmbeddedTemplate', 'priority' => 10100));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta',
            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ProviderInterface::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ProviderInterface::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs',
            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY, 'method' => 'onGalleryHtml', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION, 'method' => 'onPlayerTemplate', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10100));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10400));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10300));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10100));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10300));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10100));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10200));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_html_JsConfig',
            'tubepress_addons_core_impl_listeners_html_JsConfig'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, 'method' => 'onPreScriptsHtml', 'priority' => 10000));


        $listeners = array(

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_html_PreCssHtmlListener' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_STYLESHEETS_PRE, 'method' => 'onBeforeCssHtml', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_options_LegacyThemeListener' =>
                array('event' => tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . '.' . tubepress_api_const_options_names_Thumbs::THEME,
                    'method' => 'onPreValidationSet', 'priority' => 300000),
        );

        foreach ($listeners as $className => $tagAttributes) {

            $this->expectRegistration($className, $className)->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, $tagAttributes);
        }

        $this->expectRegistration(
            'tubepress_addons_core_impl_listeners_StringMagicFilter_preValidation',
            'tubepress_addons_core_impl_listeners_StringMagicFilter'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTION_ANY_PRE_VALIDATION_SET, 'method' => 'magic', 'priority' => 10100));

        $this->expectRegistration(
            'tubepress_addons_core_impl_listeners_StringMagicFilter_readFromExternal',
            'tubepress_addons_core_impl_listeners_StringMagicFilter'
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER,  array('event' => tubepress_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, 'method' => 'magic', 'priority' => 10000));

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams',
            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams'
            )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ProviderInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT, 'method' => 'onGalleryInitJs', 'priority' => 10000))
             ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'));
    }

    private function _pluggables()
    {
        $this->_pluggableOptionsPageParticipant();

        $this->_pluggableAjaxHandlers();

        $this->_pluggablePlayerLocations();

        $this->_pluggableShortcodeHandlers();
    }

    private function _pluggableOptionsPageParticipant()
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

            $this->expectRegistration(

                'core_options_category_' . $categoryIndex++,
                'tubepress_impl_options_ui_OptionsPageItem'
            )->withArgument($id)
             ->withArgument($displayName);
        }

        $categoryReferences = array();

        for ($x = 0 ; $x < $categoryIndex; $x++) {

            $categoryReferences[] = new tubepress_impl_ioc_Reference('core_options_category_' . $x);
        }

        $fieldIndex = 0;

        //Gallery source field
        $this->expectRegistration(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_GallerySourceField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_));

        //Filter field
        $this->expectRegistration(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_ParticipantFilterField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        //Meta multi-select
        $this->expectRegistration(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));


        //Theme field
        $this->expectRegistration(

            'core_options_field_' . $fieldIndex++,
            'tubepress_addons_core_impl_options_ui_fields_ThemeField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

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

            $this->expectRegistration('core_options_field_' . $fieldIndex++, $class)->withArgument($id);
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

        $this->expectRegistration(

            'core_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'

        )->withArgument('core')
         ->withArgument('Core')  //this will never be shown, so don't translate
         ->withArgument($categoryReferences)
         ->withArgument($fieldReferences)
         ->withArgument($map)
         ->withArgument(false)
         ->withArgument(false)
         ->withTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }

    private function _pluggableAjaxHandlers()
    {
        $this->expectRegistration('tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService', 'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_spi_http_PluggableAjaxCommandService::_);
    }

    private function _pluggablePlayerLocations()
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

            $this->expectRegistration($playerLocationClass, $playerLocationClass)
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
                ->withTag(tubepress_spi_player_PluggablePlayerLocationService::_);
        }
    }

    private function _pluggableShortcodeHandlers()
    {
        $this->expectRegistration(
            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
        ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_)
            ->withArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'));

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
        ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
             ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_)
             ->withArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'));

        $this->expectRegistration(
            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
        ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

    }

    private function _filesystemFinderFactory()
    {
        $this->expectRegistration(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }

    private function _videoCollector()
    {
        $this->expectRegistration(tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));

    }

    private function _themeHandler()
    {
        $this->expectRegistration(
            tubepress_spi_theme_ThemeHandlerInterface::_,
            'tubepress_impl_theme_ThemeHandler'
        )->withArgument('%themes%')
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_));
    }

    private function _themeFinder()
    {
        $this->expectRegistration(
            tubepress_spi_theme_ThemeFinderInterface::_,
            'tubepress_impl_theme_ThemeFinder'
        )->withArgument(new ehough_iconic_Reference('ehough_finder_FinderFactoryInterface'))
         ->withArgument(new ehough_iconic_Reference(tubepress_api_environment_EnvironmentInterface::_));
    }

    private function _templateBuilder()
    {
        $this->expectRegistration('ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder');
    }

    private function _shortcodeParser()
    {
        $this->expectRegistration(tubepress_api_shortcode_ParserInterface::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser')->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));
    }

    private function _optionMetaNameService()
    {
        $this->expectRegistration(

            tubepress_addons_core_impl_options_MetaOptionNameService::_,
            tubepress_addons_core_impl_options_MetaOptionNameService::_
        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setVideoProviders'));
    }

    private function _registerPlayerHtml()
    {
        $this->expectRegistration(tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'));

    }

    private function _optionProvider()
    {
        $this->expectRegistration(
            'tubepress_addons_core_impl_options_CoreOptionProvider',
            'tubepress_addons_core_impl_options_CoreOptionProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->withTag(tubepress_api_options_ProviderInterface::_)
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'))
         ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));
    }

    private function _hrch()
    {
        $this->expectRegistration(tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler');
    }

    private function _hrps()
    {
        $this->expectRegistration(tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService');
    }

    private function _filesystem()
    {
        $this->expectRegistration('ehough_filesystem_FilesystemInterface', 'ehough_filesystem_Filesystem');
    }

    private function _embeddedGenerator()
    {
        $this->expectRegistration(tubepress_spi_embedded_EmbeddedHtmlGenerator::_, 'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator')
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'));

    }

    private function _ajaxHandler()
    {
         $this->expectRegistration(tubepress_spi_http_AjaxHandler::_, 'tubepress_impl_http_DefaultAjaxHandler')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                 array('tag' => tubepress_spi_http_PluggableAjaxCommandService::_, 'method' => 'setPluggableAjaxCommandHandlers'));
    }

    private function _cacheService()
    {
        $this->expectRegistration(
            'tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_builderServiceId',
            'tubepress_addons_core_impl_ioc_FilesystemCacheBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));

        $def = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $this->expectDefinition('tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_actualPoolServiceId', $def);

        $this->expectRegistration(
            'ehough_stash_interfaces_PoolInterface',
            'tubepress_impl_cache_PoolDecorator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_actualPoolServiceId'));
    }
}