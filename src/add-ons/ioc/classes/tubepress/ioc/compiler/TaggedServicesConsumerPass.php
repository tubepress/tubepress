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

/**
 * Finds registered listeners and adds them to the event dispatcher.
 */
class tubepress_ioc_compiler_TaggedServicesConsumerPass implements tubepress_spi_ioc_CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $consumerIds = $containerBuilder->findTaggedServiceIds(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER);

        foreach ($consumerIds as $consumerId => $tags) {

            $consumerDefinition = $containerBuilder->getDefinition($consumerId);

            foreach ($tags as $tagData) {

                $requiredAttribites = array('tag', 'method');

                foreach ($requiredAttribites as $attributeName) {

                    if (!isset($tagData[$attributeName])) {

                        throw new LogicException(sprintf('Service %s must specify %s in its tag data', $consumerId, $attributeName));
                    }
                }

                $matchingServiceIdsToTagData = $containerBuilder->findTaggedServiceIds($tagData['tag']);
                $references                  = $this->_buildMethodCallArgument($matchingServiceIdsToTagData);

                $consumerDefinition->addMethodCall($tagData['method'], array($references));
            }
        }
    }

    private function _buildMethodCallArgument(array $matchingServiceIdsToTagData)
    {
        if (empty($matchingServiceIdsToTagData)) {

            return array();
        }

        $references = array();

        foreach ($matchingServiceIdsToTagData as $id => $attributes) {

            $references[] = new tubepress_api_ioc_Reference($id);
        }

        return $references;
    }
}
