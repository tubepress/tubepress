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
 * @covers tubepress_app_media_provider_ioc_ProviderExtension
 */
class tubepress_test_app_media_provider_ioc_MediaProviderExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_app_media_provider_ioc_ProviderExtension
     */
    protected function buildSut()
    {
        return new tubepress_app_media_provider_ioc_ProviderExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_app_media_provider_api_CollectorInterface::_,
            'tubepress_app_media_provider_impl_Collector'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'
            ));

        $this->expectRegistration(

            tubepress_app_media_provider_api_ItemSorterInterface::_,
            'tubepress_app_media_provider_impl_ItemSorter'
        );

        $this->expectRegistration(
            'tubepress_app_media_provider_impl_listeners_options_AcceptableValues',
            'tubepress_app_media_provider_impl_listeners_options_AcceptableValues'
        )->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_media_provider_api_Constants::OPTION_ORDER_BY,
                'method'   => 'onOrderBy',
                'priority' => 10300
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_media_provider_api_Constants::OPTION_GALLERY_SOURCE,
                'method'   => 'onMode',
                'priority' => 10300
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT,
                'method'   => 'onPerPageSort',
                'priority' => 10300
            ));

        $this->expectRegistration(

            'tubepress_app_media_provider_impl_listeners_page_CorePageListener',
            'tubepress_app_media_provider_impl_listeners_page_CorePageListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_media_provider_api_CollectorInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE,
                'method'   => 'perPageSort',
                'priority' => 10300
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE,
                'method'   => 'capResults',
                'priority' => 10100
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE,
                'method'   => 'blacklist',
                'priority' => 10200
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_PAGE,
                'method' => 'prependItems',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_provider', array(

            'defaultValues' => array(
                tubepress_app_media_provider_api_Constants::OPTION_GALLERY_SOURCE   => tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
                tubepress_app_media_provider_api_Constants::OPTION_ORDER_BY         => 'default',
                tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT    => tubepress_app_media_provider_api_Constants::PER_PAGE_SORT_NONE,
                tubepress_app_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP => 0,
                tubepress_app_media_provider_api_Constants::OPTION_ITEM_ID_BLACKLIST  => null,
                tubepress_app_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 20,
            ),

            'labels' => array(
                tubepress_app_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP => 'Maximum total videos to retrieve',   //>(translatable)<
                tubepress_app_media_provider_api_Constants::OPTION_ITEM_ID_BLACKLIST  => 'Video blacklist',                    //>(translatable)<
                tubepress_app_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE => 'Thumbnails per page',                //>(translatable)<
            ),

            'descriptions' => array(

                tubepress_app_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP =>
                    'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', //>(translatable)<

                tubepress_app_media_provider_api_Constants::OPTION_ITEM_ID_BLACKLIST  =>
                    'A list of video IDs that should never be displayed.',                                          //>(translatable)<

                tubepress_app_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE =>
                    sprintf('Default is %s. Maximum is %s.', 20, 50),                                               //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES .
            '_' . tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT, array(

                'optionName' => tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT,
                'priority'   => 30000,
                'values'     => array(

                    tubepress_app_media_provider_api_Constants::PER_PAGE_SORT_NONE   => 'none',                          //>(translatable)<
                    tubepress_app_media_provider_api_Constants::PER_PAGE_SORT_RANDOM => 'random',                        //>(translatable)<
                )
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_provider', array(

            'priority' => 30000,
            'map'      => array(
                'positiveInteger' => array(
                    tubepress_app_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE,
                ),
                'nonNegativeInteger' => array(
                    tubepress_app_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP,
                )
            )
        ));

        $categoryIndex = 0;
        $categoryIdsToNamesMap = array(
            tubepress_app_media_provider_api_Constants::OPTIONS_UI_CATEGORY_GALLERY_SOURCE => 'Which videos?',   //>(translatable)<
            tubepress_app_media_provider_api_Constants::OPTIONS_UI_CATEGORY_FEED           => 'Feed'             //>(translatable)<
        );
        foreach ($categoryIdsToNamesMap as $id => $name) {
            $this->expectRegistration(
                'media_provider_category_' . $categoryIndex++,
                'tubepress_app_options_ui_api_ElementInterface'
            )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($id)
                ->withArgument($name);
        }
        $categoryReferences = array();
        for ($x = 0; $x < $categoryIndex; $x++) {
            $categoryReferences[] = new tubepress_platform_api_ioc_Reference('media_provider_category_' . $x);
        }

        $fieldIndex = 0;
        $fieldMap = array(
            'text' => array(
                tubepress_app_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP,
                tubepress_app_media_provider_api_Constants::OPTION_ITEM_ID_BLACKLIST,
            ),
            'perPageSort' => array(
                tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT
            ),
            'orderBy' => array(
                tubepress_app_media_provider_api_Constants::OPTION_ORDER_BY
            )
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {
                $this->expectRegistration(
                    'media_provider_field_' . $fieldIndex++,
                    'tubepress_app_options_ui_api_FieldInterface'
                )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);
            }
        }
        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('media_provider_field_' . $x);
        }
        $fieldMap = array(
            tubepress_app_media_provider_api_Constants::OPTIONS_UI_CATEGORY_FEED => array(
                tubepress_app_media_provider_api_Constants::OPTION_ORDER_BY,
                tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT,
                tubepress_app_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP,
                tubepress_app_media_provider_api_Constants::OPTION_ITEM_ID_BLACKLIST,
            )
        );

        $this->expectRegistration(
            'tubepress_app_media_provider_impl_options_ui_FieldProvider',
            'tubepress_app_media_provider_impl_options_ui_FieldProvider'
        )->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);

        $mockField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockCategory = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_app_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockCategory);

        return array(

            tubepress_platform_api_log_LoggerInterface::_                  => $logger,
            tubepress_app_options_api_ContextInterface::_        => tubepress_app_options_api_ContextInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_  => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_app_http_api_RequestParametersInterface::_ => tubepress_app_http_api_RequestParametersInterface::_,
            tubepress_lib_translation_api_TranslatorInterface::_ => tubepress_lib_translation_api_TranslatorInterface::_,
            tubepress_app_options_ui_api_ElementBuilderInterface::_ => $elementBuilder,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder
        );
    }
}