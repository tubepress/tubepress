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
 *
 */
class tubepress_core_impl_ioc_LtrimSubjectListenersPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!$containerBuilder->hasDefinition(tubepress_core_api_event_EventDispatcherInterface::_)) {

            return;
        }

        $eventDispatcherDefinition = $containerBuilder->getDefinition(tubepress_core_api_event_EventDispatcherInterface::_);
        $taggedServiceIds          = $containerBuilder->findTaggedServiceIds(tubepress_core_api_const_ioc_Tags::LTRIM_SUBJECT_LISTENER);

        foreach ($taggedServiceIds as $serviceId => $tagData) {

            if (!isset($tagData['priority']) || !isset($tagData['charlist']) || !isset($tagData['event'])) {

                throw new LogicException('ltrim subject listeners must define event, charlist, and priority.');
            }

            $listenerId  = 'ltrim_subject_listener_' . $serviceId;
            $containerBuilder->register(

                $listenerId,
                'tubepress_core_impl_listeners_LtrimSubjectListener'
            )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
             ->addArgument($tagData['charlist']);

            $eventDispatcherDefinition->addMethodCall(

                'addListenerService',
                array($tagData['event'], array($serviceId, 'execute'), $tagData['priority'])
            );
        }
    }
}