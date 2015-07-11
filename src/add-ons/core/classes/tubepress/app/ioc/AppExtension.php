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
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerPlayers($containerBuilder);
        $this->_registerVendorServices($containerBuilder);
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $listenerData = array(

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
            'tubepress_app_impl_listeners_http_UserAgentListener' => array(
                tubepress_app_api_environment_EnvironmentInterface::_
            ),


            /**
             * OPTIONS VALUES
             */
            'tubepress_app_impl_listeners_options_values_FeedOptions'    => array(),
            'tubepress_app_impl_listeners_options_values_PerPageSort'    => array(),


            /**
             * PLAYER
             */
            'tubepress_app_impl_listeners_player_PlayerListener' => array(
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_template_TemplatingInterface::_,
            ),
        );

        $servicesConsumers = array(
            'tubepress_app_impl_listeners_options_values_FeedOptions' => array(
                tubepress_app_api_media_MediaProviderInterface::__ => 'setMediaProviders',
            ),


            'tubepress_app_impl_listeners_player_PlayerListener' => array(
                'tubepress_app_api_player_PlayerLocationInterface' => 'setPlayerLocations',
            ),

        );

        $listeners = array(

            /**
             * GALLERY INIT JS
             */
            tubepress_app_api_event_Events::GALLERY_INIT_JS => array(
                96000  => array('tubepress_app_impl_listeners_player_PlayerListener'     => 'onGalleryInitJs'),
            ),

            /**
             * HTML
             */
            tubepress_app_api_event_Events::HTML_GENERATION => array(
                98000  => array('tubepress_app_impl_listeners_html_generation_SoloPlayerListener' => 'onHtmlGeneration'),
                94000  => array('tubepress_app_impl_listeners_html_generation_SingleItemListener' => 'onHtmlGeneration',),
            ),
            tubepress_app_api_event_Events::HTML_GLOBAL_JS_CONFIG => array(
                100000 => array('tubepress_app_impl_listeners_html_jsconfig_BaseUrlSetter' => 'onGlobalJsConfig',)
            ),

            /**
             * HTTP
             */
            tubepress_app_api_event_Events::HTTP_AJAX . '.playerHtml' => array(
                100000 => array('tubepress_app_impl_listeners_http_ajax_PlayerAjaxCommand' => 'onAjax')
            ),

            /**
             * MEDIA
             */
            tubepress_app_api_event_Events::MEDIA_PAGE_NEW => array(
                92000  => array('tubepress_app_impl_listeners_player_PlayerListener' => 'onNewMediaPage'),
            ),


            /**
             * OPTIONS VALUES
             */


            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_ORDER_BY => array(
                100000 => array('tubepress_app_impl_listeners_options_values_FeedOptions' => 'onOrderBy')
            ),
            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::GALLERY_SOURCE => array(
                100000 => array('tubepress_app_impl_listeners_options_values_FeedOptions' => 'onMode')
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


            /**
             * TEMPLATE - PRE
             */
            tubepress_app_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main' => array(
                94000 => array('tubepress_app_impl_listeners_player_PlayerListener'            => 'onGalleryTemplatePreRender')
            ),


        );

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

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__core',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_app_api_options_Names::DEBUG_ON                            => true,
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
                tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => null,
                tubepress_app_api_options_Names::PLAYER_LOCATION                     => 'normal',
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD                   => 'tubepress',
                tubepress_app_api_options_Names::SINGLE_MEDIA_ITEM_ID                => null,
                tubepress_app_api_options_Names::SOURCES                             => null,

            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_app_api_options_Names::DEBUG_ON                            => 'Enable debugging',   //>(translatable)<
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
                tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
                tubepress_app_api_options_Names::PLAYER_LOCATION                     => 'Play each video',      //>(translatable)<
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD                   => 'Shortcode keyword',  //>(translatable)<

            ),

            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_app_api_options_Names::DEBUG_ON                    => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
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
                tubepress_app_api_options_Names::SOURCES,
            ),
        ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_app_api_options_Names::HTML_GALLERY_ID,
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
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
    }

    private function _registerOptionsUiFieldProvider(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE,
                tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW,
                tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT,
                tubepress_app_api_options_Names::HTML_HTTPS,
                tubepress_app_api_options_Names::DEBUG_ON,
            ),
            'dropdown' => array(
                tubepress_app_api_options_Names::PLAYER_LOCATION,
                tubepress_app_api_options_Names::HTTP_METHOD,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
            ),
            'text' => array(
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST,
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
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
            array(tubepress_app_api_options_ui_CategoryNames::FEED,           'Feed'),          //>(translatable)<
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
            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(
                tubepress_app_api_options_Names::PLAYER_LOCATION,
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
            tubepress_app_api_options_ui_CategoryNames::FEED => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::FEED_ORDER_BY,
                tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
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
}
