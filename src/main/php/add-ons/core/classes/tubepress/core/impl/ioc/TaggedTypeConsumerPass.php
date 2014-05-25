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
class tubepress_core_impl_ioc_TaggedTypeConsumerPass extends tubepress_core_impl_ioc_AbstractTagProcessingPass
{
    /**
     * @return string
     */
    protected function getTag()
    {
        return tubepress_core_api_const_ioc_Tags::TAGGED_SERVICE_CONSUMER;
    }

    /**
     * @return string[]
     */
    protected function getAdditionalRequiredTagAttributes()
    {
        return array('type');
    }

    protected function buildMethodCallArgument(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, array $tagData, array $matchingServiceIdsToTagData)
    {
        $required = isset($tagData['required']) ? (bool) $tagData['required'] : true;

        if (empty($matchingServiceIdsToTagData)) {

            if ($required) {

                throw new LogicException(sprintf('No services match tag %s and type %s', $tagData['tag'], $tagData['type']));
            }

            return null;
        }

        if (count($matchingServiceIdsToTagData) > 1) {

            throw new LogicException(sprintf('There are multiple services of type %s tagged with %s', $tagData['type'], $tagData['tag']));
        }

        $keys = array_keys($matchingServiceIdsToTagData);

        return new tubepress_api_ioc_Reference($keys[0]);
    }
}