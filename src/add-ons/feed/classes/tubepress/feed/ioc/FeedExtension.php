<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_feed_ioc_FeedExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_feed_impl_listeners_AcceptableValuesListener',
            'tubepress_feed_impl_listeners_AcceptableValuesListener'
        )->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::FEED_ORDER_BY,
            'priority' => 100000,
            'method'   => 'onOrderBy',
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::GALLERY_SOURCE,
            'priority' => 100000,
            'method'   => 'onMode',
        ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::FEED_PER_PAGE_SORT,
            'priority' => 100000,
            'method'   => 'onPerPageSort',
        ))->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders',
        ));
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__feed',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                tubepress_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE => null,
                tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST         => null,
                tubepress_api_options_Names::FEED_ORDER_BY                  => 'default',
                tubepress_api_options_Names::FEED_PER_PAGE_SORT             => tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NONE,
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP          => 0,
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE          => 20,
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST => 'Video blacklist',                    //>(translatable)<
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP  => 'Maximum total videos to retrieve',   //>(translatable)<
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE  => 'Thumbnails per page',                //>(translatable)<,
                tubepress_api_options_Names::FEED_PER_PAGE_SORT     => 'Per-page sort order',                //>(translatable)<
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST => 'A list of video IDs that should never be displayed.',                                          //>(translatable)<
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP  => 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', //>(translatable)<
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE  => sprintf('Default is %s. Maximum is %s.', 20, 50),                                               //>(translatable)<
                tubepress_api_options_Names::FEED_PER_PAGE_SORT     => 'Additional sort order applied to each individual page of a gallery',                           //>(translatable)<

            ),
        ))->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_NO_PERSIST => array(
                tubepress_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE,
            ),
        ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOption',
                ));
            }
        }

        $fixedValuesMap = array(
            tubepress_api_options_Names::FEED_PER_PAGE_SORT => array(
                tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NONE   => 'none',           //>(translatable)<
                tubepress_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM => 'random',         //>(translatable)<
            ),
        );
        foreach ($fixedValuesMap as $optionName => $valuesMap) {
            $containerBuilder->register(
                'fixed_values.' . $optionName,
                'tubepress_api_options_listeners_FixedValuesListener'
            )->addArgument($valuesMap)
             ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'priority' => 100000,
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                'method'   => 'onAcceptableValues',
            ));
        }
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'dropdown' => array(
                tubepress_api_options_Names::FEED_PER_PAGE_SORT,
            ),
            'text' => array(
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST,
            ),
            'orderBy' => array(
                tubepress_api_options_Names::FEED_ORDER_BY,
            ),
            'multiSourceText' => array(
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'feed_field_' . $id;

                $containerBuilder->register(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::FEED, 'Feed'),          //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'feed_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
             ->addArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::FEED => array(
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_api_options_Names::FEED_ORDER_BY,
                tubepress_api_options_Names::FEED_PER_PAGE_SORT,
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__feed',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-feed')
         ->addArgument('Feed')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
