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
 * @api
 * @since 4.0.0
 */
class tubepress_platform_impl_boot_helper_uncached_contrib_SerializedRegistry implements tubepress_platform_api_contrib_RegistryInterface
{
    /**
     * @var
     */
    private $_nameToInstanceMap;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    public function __construct(array $bootArtifacts, $key,
                                tubepress_platform_api_log_LoggerInterface $logger,
                                tubepress_platform_impl_boot_helper_uncached_Serializer $serializer)
    {
        $this->_logger            = $logger;
        $this->_nameToInstanceMap = array();

        if (!isset($bootArtifacts[$key])) {

            throw new InvalidArgumentException("$key not found in boot artifacts");
        }

        $contributables = $serializer->unserialize($bootArtifacts[$key]);

        if (!is_array($contributables)) {

            throw new InvalidArgumentException('Expected to deserialize an array');
        }

        foreach ($contributables as $contributable) {

            if (!($contributable instanceof tubepress_platform_api_contrib_ContributableInterface)) {

                throw new InvalidArgumentException('Unserialized data contained a non contributable');
            }

            $name                            = $contributable->getName();
            $this->_nameToInstanceMap[$name] = $contributable;
        }
    }

    /**
     * @return tubepress_platform_api_contrib_ContributableInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getAll()
    {
        return array_values($this->_nameToInstanceMap);
    }

    /**
     * @param $name string The name of the contributable to return.
     *
     * @throws InvalidArgumentException
     *
     * @return tubepress_platform_api_contrib_ContributableInterface
     *
     * @api
     * @since 4.0.0
     */
    public function getInstanceByName($name)
    {
        if (!isset($this->_nameToInstanceMap[$name])) {

            throw new InvalidArgumentException();
        }

        return $this->_nameToInstanceMap[$name];
    }
}