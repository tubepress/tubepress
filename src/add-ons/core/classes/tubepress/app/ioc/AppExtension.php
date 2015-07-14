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
        $this->_registerOptions($containerBuilder);
        $this->_registerVendorServices($containerBuilder);
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
