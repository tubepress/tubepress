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
class tubepress_feed_ioc_FeedExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_feed_impl_listeners_FeedOptions',
            'tubepress_feed_impl_listeners_FeedOptions'
        )->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_ORDER_BY,
            'priority' => 100000,
            'method'   => 'onOrderBy'
        ))->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::GALLERY_SOURCE,
            'priority' => 100000,
            'method'   => 'onMode'
        ))->addTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_api_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders'
        ));

        $containerBuilder->register(
            'tubepress_feed_impl_listeners_PerPageSort',
            'tubepress_feed_impl_listeners_PerPageSort'
        )->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event' => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::FEED_PER_PAGE_SORT,
            'priority' => 100000,
            'method' => 'onAcceptableValues'
        ));
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {

    }

    private function _registerOptionsUi(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {

    }
}