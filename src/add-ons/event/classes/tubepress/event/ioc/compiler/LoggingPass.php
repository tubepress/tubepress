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

class tubepress_event_ioc_compiler_LoggingPass implements tubepress_spi_ioc_CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!$containerBuilder->hasDefinition(tubepress_api_event_EventDispatcherInterface::_) ||
            !$containerBuilder->has('tubepress_internal_logger_BootLogger')) {

            return;
        }

        $eventDispatcherDefinition = $containerBuilder->getDefinition(tubepress_api_event_EventDispatcherInterface::_);
        $logger                    = $containerBuilder->get('tubepress_internal_logger_BootLogger');

        $clazz = $eventDispatcherDefinition->getClass();

        $logger->debug(sprintf('Event dispatcher is of type <code>%s</code>', $clazz));

        $methodCalls = $eventDispatcherDefinition->getMethodCalls();
        $events      = array();

        foreach ($methodCalls as $methodCall) {

            $methodName = $methodCall[0];
            $details    = $methodCall[1];

            if ($methodName !== 'addListenerService') {

                continue;
            }

            $eventName         = $details[0];
            $callbackServiceId = $details[1][0];
            $callbackMethod    = $details[1][1];
            $priority          = count($details) > 2 ? $details[2] : 0;

            if (!$containerBuilder->hasDefinition($callbackServiceId)) {

                $logger->error(sprintf('Event listener service ID <code>%s</code> does not exist.', $callbackServiceId));
                continue;
            }

            $callbackDefinition = $containerBuilder->getDefinition($callbackServiceId);
            $callbackClass      = $callbackDefinition->getClass();

            if (!isset($events[$eventName])) {

                $events[$eventName] = array();
            }

            $events[$eventName][$priority] = sprintf('%s::%s', $callbackClass, $callbackMethod);
        }

        $logger->debug(sprintf('There are %d events that can be triggered. Details follow...', count($events)));

        ksort($events);

        foreach ($events as $eventName => $listeners) {

            krsort($listeners);

            $logger->debug(sprintf('<code>&nbsp;&nbsp;%s</code>', $eventName));

            foreach ($listeners as $priority => $listener) {

                $logger->debug(sprintf('<code>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;priority %d - %s</code>', $priority, $listener));
            }
        }
    }

}
