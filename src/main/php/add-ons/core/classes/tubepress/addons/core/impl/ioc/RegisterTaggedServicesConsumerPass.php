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
class tubepress_addons_core_impl_ioc_RegisterTaggedServicesConsumerPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * Provides add-ons with the ability to modify the TubePress IOC container builder
     * before it is compiled for production.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The core IOC container builder.
     *
     * @throws InvalidArgumentException If a service tag doesn't include the event attribute.
     *
     * @api
     * @since 3.1.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $consumerIds = $containerBuilder->findTaggedServiceIds(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER);

        foreach ($consumerIds as $consumerId => $tags) {

            $consumerDefinition = $containerBuilder->getDefinition($consumerId);

            foreach ($tags as $tagData) {

                if (!isset($tagData['tag'])) {

                    throw new InvalidArgumentException('Tagged set consumers must specify which tagged services they would like to consume');
                }

                $matchingServiceIds = $containerBuilder->findTaggedServiceIds($tagData['tag']);

                if (empty($matchingServiceIds)) {

                    continue;
                }

                if (!isset($tagData['method'])) {

                    throw new InvalidArgumentException('Tagged set consumers must specify which method to run for each tagged service ID');
                }

                $references = array();

                foreach ($matchingServiceIds as $id => $attributes) {

                    $references[] = new tubepress_impl_ioc_Reference($id);
                }

                $consumerDefinition->addMethodCall($tagData['method'], array($references));
            }
        }
    }
}