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

interface tubepress_api_ioc_ContainerBuilderInterface extends tubepress_api_ioc_ContainerInterface
{
    const _ = 'tubepress_api_ioc_ContainerBuilderInterface';

    /**
     * Adds the service definitions.
     *
     * @param tubepress_api_ioc_DefinitionInterface[] $definitions An array of service definitions
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    function addDefinitions(array $definitions);

    /**
     * Returns service ids for a given tag.
     *
     * @param string $name The tag name
     *
     * @return array An array of tags
     *
     * @api
     * @since 3.1.0
     */
    function findTaggedServiceIds($name);

    /**
     * Returns all tags the defined services use.
     *
     * @return array An array of tags
     *
     * @api
     * @since 3.1.0
     */
    function findTags();

    /**
     * Gets a service definition.
     *
     * @param string $id The service identifier
     *
     * @return tubepress_api_ioc_DefinitionInterface A tubepress_api_ioc_DefinitionInterface instance, or null if the
     *                                               service does not exist.
     *
     * @api
     * @since 3.1.0
     */
    function getDefinition($id);

    /**
     * Gets all service definitions.
     *
     * @return tubepress_api_ioc_DefinitionInterface[] An array of tubepress_api_ioc_DefinitionInterface instances
     *
     * @api
     * @since 3.1.0
     */
    function getDefinitions();

    /**
     * Returns true if a service definition exists under the given identifier.
     *
     * @param string $id The service identifier
     *
     * @return Boolean true if the service definition exists, false otherwise
     *
     * @api
     * @since 3.1.0
     */
    function hasDefinition($id);

    /**
     * Registers a service definition.
     *
     * This methods allows for simple registration of service definition
     * with a fluid interface.
     *
     * @param string $id    The service identifier
     * @param string $class The service class
     *
     * @return tubepress_api_ioc_DefinitionInterface A tubepress_api_ioc_DefinitionInterface instance
     *
     * @api
     * @since 3.1.0
     */
    function register($id, $class = null);

    /**
     * Removes a service definition.
     *
     * @param string $id The service identifier
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    function removeDefinition($id);

    /**
     * Sets a service definition.
     *
     * @param string                                $id         The service identifier
     * @param tubepress_api_ioc_DefinitionInterface $definition A tubepress_api_ioc_DefinitionInterface instance
     *
     * @return tubepress_api_ioc_DefinitionInterface the service definition
     *
     * @throws BadMethodCallException When this ContainerBuilder is frozen
     *
     * @api
     * @since 3.1.0
     */
    function setDefinition($id, tubepress_api_ioc_DefinitionInterface $definition);

    /**
     * Sets the service definitions.
     *
     * @param tubepress_api_ioc_DefinitionInterface[] $definitions An array of service definitions
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    function setDefinitions(array $definitions);
}