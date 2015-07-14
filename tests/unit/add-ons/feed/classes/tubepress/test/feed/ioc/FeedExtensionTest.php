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
class tubepress_test_feed_ioc_FeedExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
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
        )->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_ORDER_BY,
            'priority' => 100000,
            'method'   => 'onOrderBy'
        ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::GALLERY_SOURCE,
            'priority' => 100000,
            'method'   => 'onMode'
        ))->withTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_api_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders'
        ));

        $this->expectRegistration(
            'tubepress_feed_impl_listeners_PerPageSort',
            'tubepress_feed_impl_listeners_PerPageSort'
        )->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event' => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
            'priority' => 100000,
            'method' => 'onAcceptableValues'
        ));
    }

    private function _registerOptions()
    {

    }

    private function _registerOptionsUi()
    {

    }

    protected function getExpectedExternalServicesMap()
    {
        return array();
    }
}
