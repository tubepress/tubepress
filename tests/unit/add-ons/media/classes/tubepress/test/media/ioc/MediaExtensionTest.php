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
 * @covers tubepress_media_ioc_MediaExtension
 */
class tubepress_test_media_ioc_MediaExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_media_ioc_MediaExtension
     */
    protected function buildSut()
    {
        return  new tubepress_media_ioc_MediaExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerServices();
    }

    private function _registerServices()
    {
        $this->expectRegistration(
            tubepress_app_api_media_AttributeFormatterInterface::_,
            'tubepress_media_impl_AttributeFormatter'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_util_TimeUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));

        $this->expectRegistration(
            tubepress_app_api_media_CollectorInterface::_,
            'tubepress_media_impl_Collector'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_));

        $this->expectRegistration(
            tubepress_app_api_media_HttpCollectorInterface::_,
            'tubepress_media_impl_HttpCollector'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_HttpClientInterface::_));
    }
    
    private function _registerListeners()
    {
        $listenerData = array(

            'tubepress_media_impl_listeners_PageListener' => array(
                tubepress_platform_api_log_LoggerInterface::_,
                tubepress_app_api_options_ContextInterface::_,
                tubepress_lib_api_http_RequestParametersInterface::_,
                tubepress_app_api_media_CollectorInterface::_,
                tubepress_platform_api_url_UrlFactoryInterface::_,
            ),
            'tubepress_media_impl_listeners_CollectionListener' => array(),
            'tubepress_media_impl_listeners_DispatchingListener' => array(
                tubepress_lib_api_event_EventDispatcherInterface::_
            ),
        );

        $servicesConsumers = array(
            'tubepress_media_impl_listeners_CollectionListener' => array(
                tubepress_app_api_media_MediaProviderInterface::__ => 'setMediaProviders'
            ),
        );

        $listeners = array(
            tubepress_app_api_event_Events::MEDIA_PAGE_NEW => array(
                100000 => array('tubepress_media_impl_listeners_PageListener' => 'perPageSort'),
                98000  => array('tubepress_media_impl_listeners_PageListener' => 'blacklist'),
                96000  => array('tubepress_media_impl_listeners_PageListener' => 'capResults'),
                94000  => array('tubepress_media_impl_listeners_PageListener' => 'prependItems'),
                93000  => array('tubepress_media_impl_listeners_PageListener' => 'filterDuplicates'),
            ),
            tubepress_app_api_event_Events::MEDIA_PAGE_REQUEST => array(
                100000 => array('tubepress_media_impl_listeners_CollectionListener'  => 'onMediaPageRequest'),
                98000  => array('tubepress_media_impl_listeners_DispatchingListener' => 'onMediaPageRequest'),
            ),
            tubepress_app_api_event_Events::MEDIA_ITEM_REQUEST => array(
                100000 => array('tubepress_media_impl_listeners_CollectionListener'  => 'onMediaItemRequest'),
                98000  => array('tubepress_media_impl_listeners_DispatchingListener' => 'onMediaItemRequest'),
            ),
        );

        foreach ($listenerData as $serviceId => $args) {

            $def = $this->expectRegistration($serviceId, $serviceId);

            foreach ($args as $argumentId) {

                $def->withArgument(new tubepress_platform_api_ioc_Reference($argumentId));
            }
        }

        foreach ($listeners as $eventName => $eventListeners) {
            foreach ($eventListeners as $priority => $listenerList) {
                foreach ($listenerList as $serviceId => $method) {

                    $def = $this->getDefinition($serviceId);

                    if ($def === null) {

                        throw new LogicException("Cannot find definition for $serviceId");
                    }

                    $def->shouldReceive('addTag')->once()->with(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(

                        'event'    => $eventName,
                        'method'   => $method,
                        'priority' => $priority
                    ));
                }
            }
        }

        foreach ($servicesConsumers as $serviceId => $consumptionData) {
            foreach ($consumptionData as $tag => $method) {

                $def = $this->getDefinition($serviceId);

                $def->shouldReceive('addTag')->once()->with(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                    'tag'    => $tag,
                    'method' => $method
                ));
            }
        }
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_platform_api_log_LoggerInterface::_         => $logger,
            tubepress_lib_api_event_EventDispatcherInterface::_   => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_lib_api_http_HttpClientInterface::_         => tubepress_lib_api_http_HttpClientInterface::_,
            tubepress_app_api_options_ContextInterface::_         => tubepress_app_api_options_ContextInterface::_,
            tubepress_lib_api_util_TimeUtilsInterface::_          => tubepress_lib_api_util_TimeUtilsInterface::_,
            tubepress_lib_api_translation_TranslatorInterface::_  => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_app_api_environment_EnvironmentInterface::_ => tubepress_app_api_environment_EnvironmentInterface::_,
            tubepress_lib_api_http_RequestParametersInterface::_  => tubepress_lib_api_http_RequestParametersInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_     => tubepress_platform_api_url_UrlFactoryInterface::_,
        );
    }
}
