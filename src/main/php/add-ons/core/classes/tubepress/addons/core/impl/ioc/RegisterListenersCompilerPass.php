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
 * Finds registered listeners and adds them to the event dispatcher.
 */
class tubepress_addons_core_impl_ioc_RegisterListenersCompilerPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * Provides add-ons with the ability to modify the TubePress IOC container
     * before it is put into production.
     *
     * @param tubepress_api_ioc_ContainerInterface $container The core IOC container.
     *
     * @throws InvalidArgumentException If a service tag doesn't include the event attribute.
     *
     * @api
     * @since 3.1.0
     */
    public function process(tubepress_api_ioc_ContainerInterface $container)
    {
        if (!$container->hasDefinition(tubepress_api_event_EventDispatcherInterface::_)) {

            return;
        }

        $eventDispatcherDefinition = $container->getDefinition(tubepress_api_event_EventDispatcherInterface::_);
        $listenerServiceIds        = $container->findTaggedServiceIds(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER);

        foreach ($listenerServiceIds as $serviceId => $events) {

            foreach ($events as $event) {

                $priority = isset($event['priority']) ? $event['priority'] : 0;

                if (!isset($event['event'])) {

                    throw new InvalidArgumentException(sprintf('Service "%s" must define the "event" attribute on "%s" tags.',
                        $serviceId, tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER));
                }

                if (!isset($event['method'])) {

                    $onSuffix = preg_replace_callback(

                        array('/(?<=\b)[a-z]/i', '/[^a-z0-9]/i'),
                        array($this, '_callbackStrToUpper'),
                        $event['event']
                    );

                    $event['method'] = 'on' . $onSuffix;
                    $event['method'] = preg_replace('/[^a-z0-9]/i', '', $event['method']);
                }

                $eventDispatcherDefinition->addMethodCall(

                    'addListenerService',
                    array($event['event'], array($serviceId, $event['method']), $priority)
                );
            }
        }
    }

    public function _callbackStrToUpper($matches)
    {
        return strtoupper($matches[0]);
    }
}