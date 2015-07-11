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
class tubepress_media_ioc_MediaExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerServices($containerBuilder);
        $this->_registerListeners($containerBuilder);
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
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

    private function _registerServices(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_app_api_media_AttributeFormatterInterface::_,
            'tubepress_media_impl_AttributeFormatter'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_util_TimeUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));

        $containerBuilder->register(
            tubepress_app_api_media_CollectorInterface::_,
            'tubepress_media_impl_Collector'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_));

        $containerBuilder->register(
            tubepress_app_api_media_HttpCollectorInterface::_,
            'tubepress_media_impl_HttpCollector'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_HttpClientInterface::_));
    }
}