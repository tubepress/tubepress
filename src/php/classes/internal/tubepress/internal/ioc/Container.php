<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_internal_ioc_Container implements tubepress_api_ioc_ContainerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $_underlyingSymfonyContainer;

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $delegate)
    {
        $this->_underlyingSymfonyContainer = $delegate;
    }

    /**
     * Get a service.
     *
     * @param string $id The service identifier
     *
     * @return object The associated service, or null if not defined.
     *
     * @api
     * @since 4.0.0
     */
    public function get($id)
    {
        return $this->_underlyingSymfonyContainer->get($id, \Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    /**
     * Get a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     *
     * @api
     * @since 4.0.0
     */
    public function getParameter($name)
    {
        return $this->_underlyingSymfonyContainer->getParameter($name);
    }

    /**
     * Determine if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return Boolean true if the service is defined, false otherwise
     *
     * @api
     * @since 4.0.0
     */
    public function has($id)
    {
        return $this->_underlyingSymfonyContainer->has($id);
    }

    /**
     * Check if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return Boolean The presence of parameter in container
     *
     * @api
     * @since 4.0.0
     */
    public function hasParameter($name)
    {
        return $this->_underlyingSymfonyContainer->hasParameter($name);
    }

    /**
     * Set a service.
     *
     * @param string $id      The service identifier
     * @param object $service The service instance
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function set($id, $service)
    {
        $this->_underlyingSymfonyContainer->set($id, $service);
    }

    /**
     * Set a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setParameter($name, $value)
    {
        $this->_underlyingSymfonyContainer->setParameter($name, $value);
    }
}