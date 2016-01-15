<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Provides utility functions used during construction of a {@link tubepress_api_ioc_ContainerInterface} instance.
 *
 * @package TubePress\IoC
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_ioc_ContainerBuilderInterface extends tubepress_api_ioc_ContainerInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_ioc_ContainerBuilderInterface';

    /**
     * Add service definitions.
     *
     * @param tubepress_api_ioc_DefinitionInterface[] $definitions An array of service definitions.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function addDefinitions(array $definitions);

    /**
     * Find service ids for a given tag.
     *
     * @param string $name The tag name
     *
     * @return array An array of tags
     *
     * @api
     * @since 4.0.0
     */
    function findTaggedServiceIds($name);

    /**
     * Find all tags in use.
     *
     * @return array An array of tags
     *
     * @api
     * @since 4.0.0
     */
    function findTags();

    /**
     * Get a service definition.
     *
     * @param string $id The service identifier
     *
     * @return tubepress_api_ioc_DefinitionInterface A `tubepress_api_ioc_DefinitionInterface` instance, or null if the
     *                                               service does not exist.
     *
     * @api
     * @since 4.0.0
     */
    function getDefinition($id);

    /**
     * Get all service definitions.
     *
     * @return tubepress_api_ioc_DefinitionInterface[] An array of `tubepress_api_ioc_DefinitionInterface` instances
     *
     * @api
     * @since 4.0.0
     */
    function getDefinitions();

    /**
     * Get all parameter names.
     *
     * @return string[] An array of all parameter names. May be empty.
     */
    function getParameterNames();

    /**
     * Determine if a service definition exists under the given identifier.
     *
     * @param string $id The service identifier
     *
     * @return Boolean true if the service definition exists, false otherwise
     *
     * @api
     * @since 4.0.0
     */
    function hasDefinition($id);

    /**
     * Register a service definition.
     *
     * This methods allows for simple registration of service definition
     * with a fluid interface.
     *
     * @param string $id    The service identifier
     * @param string $class The service class
     *
     * @return tubepress_api_ioc_DefinitionInterface A `tubepress_api_ioc_DefinitionInterface` instance
     *
     * @api
     * @since 4.0.0
     */
    function register($id, $class = null);

    /**
     * Remove a service definition.
     *
     * @param string $id The service identifier
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function removeDefinition($id);

    /**
     * Set a service definition.
     *
     * @param string                                $id         The service identifier
     * @param tubepress_api_ioc_DefinitionInterface $definition The service definition.
     *
     * @return tubepress_api_ioc_DefinitionInterface The service definition
     *
     * @throws BadMethodCallException When this ContainerBuilder is frozen
     *
     * @api
     * @since 4.0.0
     */
    function setDefinition($id, tubepress_api_ioc_DefinitionInterface $definition);

    /**
     * Set the service definitions.
     *
     * @param tubepress_api_ioc_DefinitionInterface[] $definitions An array of service definitions
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setDefinitions(array $definitions);
}