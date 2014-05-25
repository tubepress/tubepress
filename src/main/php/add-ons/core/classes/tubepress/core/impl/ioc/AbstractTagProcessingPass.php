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
abstract class tubepress_core_impl_ioc_AbstractTagProcessingPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $consumerIds = $containerBuilder->findTaggedServiceIds($this->getTag());

        foreach ($consumerIds as $consumerId => $tags) {

            $consumerDefinition = $containerBuilder->getDefinition($consumerId);

            foreach ($tags as $tagData) {

                $requiredAttribites = array_merge(array('tag', 'method'), $this->getAdditionalRequiredTagAttributes());

                foreach ($requiredAttribites as $attributeName) {

                    if (!isset($tagData[$attributeName])) {

                        throw new LogicException(sprintf('Service %s must specify %s in its tag data', $consumerId, $attributeName));
                    }
                }

                $matchingServiceIdsToTagData = $containerBuilder->findTaggedServiceIds($tagData['tag']);
                $references                  = $this->buildMethodCallArgument($containerBuilder, $tagData, $matchingServiceIdsToTagData);

                $consumerDefinition->addMethodCall($tagData['method'], array($references));
            }
        }
    }

    /**
     * @return string
     */
    protected abstract function getTag();

    /**
     * @return string[]
     */
    protected abstract function getAdditionalRequiredTagAttributes();

    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder
     * @param string[]                                    $matchingServiceIds
     *
     * @return mixed
     */
    protected abstract function buildMethodCallArgument(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, array $tagData, array $matchingServiceIdsToTagData);
}