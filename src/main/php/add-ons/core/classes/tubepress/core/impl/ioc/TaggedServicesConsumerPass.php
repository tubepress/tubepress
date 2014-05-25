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
class tubepress_core_impl_ioc_TaggedServicesConsumerPass extends tubepress_core_impl_ioc_AbstractTagProcessingPass
{
    /**
     * @return string
     */
    protected function getTag()
    {
        return tubepress_core_api_const_ioc_Tags::TAGGED_SERVICES_CONSUMER;
    }

    /**
     * @return string[]
     */
    protected function getAdditionalRequiredTagAttributes()
    {
        return array();
    }

    protected function buildMethodCallArgument(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, array $tagData, array $matchingServiceIdsToTagData)
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