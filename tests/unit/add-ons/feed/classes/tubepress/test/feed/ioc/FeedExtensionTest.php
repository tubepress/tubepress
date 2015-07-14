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
 * @covers tubepress_feed_ioc_FeedExtension
 */
class tubepress_test_feed_ioc_FeedExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_feed_ioc_FeedExtension
     */
    protected function buildSut()
    {
        return  new tubepress_feed_ioc_FeedExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerOptions();
        $this->_registerOptionsUi();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_feed_impl_listeners_FeedOptions',
            'tubepress_feed_impl_listeners_FeedOptions'
        )->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::FEED_ORDER_BY,
            'priority' => 100000,
            'method'   => 'onOrderBy'
        ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::GALLERY_SOURCE,
            'priority' => 100000,
            'method'   => 'onMode'
        ))->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_api_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders'
        ));

        $this->expectRegistration(
            'tubepress_feed_impl_listeners_PerPageSort',
            'tubepress_feed_impl_listeners_PerPageSort'
        )->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event' => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::FEED_PER_PAGE_SORT,
            'priority' => 100000,
            'method' => 'onAcceptableValues'
        ));
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__feed',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

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
            ))->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_NO_PERSIST => array(
                    tubepress_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE,
                ),
            ));

        $toValidate = array(
            tubepress_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE,
            ),
            tubepress_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_api_listeners_options_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
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
            )
        );
        foreach ($fixedValuesMap as $optionName => $valuesMap) {
            $this->expectRegistration(
                'fixed_values.' . $optionName,
                'tubepress_api_listeners_options_FixedValuesListener'
            )->withArgument($valuesMap)
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'priority' => 100000,
                    'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                    'method'   => 'onAcceptableValues'
                ));
        }
    }

    private function _registerOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'dropdown' => array(
                tubepress_api_options_Names::FEED_PER_PAGE_SORT,
            ),
            'text' => array(
                tubepress_api_options_Names::FEED_RESULT_COUNT_CAP,
                tubepress_api_options_Names::FEED_ITEM_ID_BLACKLIST,
            ),
            'orderBy' => array(
                tubepress_api_options_Names::FEED_ORDER_BY
            ),
            'multiSourceText' => array(
                tubepress_api_options_Names::FEED_RESULTS_PER_PAGE,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'feed_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories = array(
            array(tubepress_api_options_ui_CategoryNames::FEED, 'Feed'),          //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'feed_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

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

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__feed',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-feed')
            ->withArgument('Feed')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_api_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
        );
    }
}
