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
  * A service definition. This informs a {@link tubepress_api_ioc_ContainerBuilderInterface} how to construct
  * the service.
  *
  * @package TubePress\IoC
  *
  * @api
  * @since 4.0.0
  */
interface tubepress_api_ioc_DefinitionInterface
{
    /**
     * Add an argument to pass to the service constructor/factory method.
     *
     * @param mixed $argument An argument
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function addArgument($argument);
    
    /**
     * Add a method to call after service initialization.
     *
     * @param string $method    The method name to call
     * @param array  $arguments An array of arguments to pass to the method call
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws InvalidArgumentException on empty `$method` param
     *
     * @api
     * @since 4.0.0
     */
    function addMethodCall($method, array $arguments = array());

    /**
     * Add a tag for this definition.
     *
     * @param string $name       The tag name
     * @param array  $attributes An array of attributes
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function addTag($name, array $attributes = array());

    /**
     * Clear all tags for a given name.
     *
     * @param string $name The tag name
     *
     * @return tubepress_api_ioc_DefinitionInterface
     *
     * @api
     * @since 4.0.0
     */
    function clearTag($name);

    /**
     * Clear the tags for this definition.
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function clearTags();
    
    /**
     * Get an argument to pass to the service constructor/factory method.
     *
     * @param integer $index The index of the argument you wish to obtain
     *
     * @return mixed The argument value
     *
     * @throws OutOfBoundsException When the argument does not exist
     *
     * @api
     * @since 4.0.0
     */
    function getArgument($index);

    /**
     * Get the arguments to pass to the service constructor/factory method.
     *
     * @return array The array of arguments
     *
     * @api
     * @since 4.0.0
     */
    function getArguments();

    /**
     * Get the service class.
     *
     * @return string The service class
     *
     * @api
     * @since 4.0.0
     */
    function getClass();
    
    /**
     * Get the configurator to call after the service is fully initialized.
     *
     * @return callable The PHP callable to call
     *
     * @api
     * @since 4.0.0
     */
    function getConfigurator();

    /**
     * Gets the service that decorates this service.
     *
     * @return null|array An array composed of the decorated service id and the new id for it, null if no service is decorated
     *
     * @api
     * @since 4.0.0
     */
    function getDecoratedService();

    /**
     * Get the factory class.
     *
     * @return string The factory class name
     *
     * @api
     * @since 4.0.0
     */
    function getFactoryClass();

    /**
     * Get the file to require before creating the service.
     *
     * @return string The full pathname to include
     *
     * @api
     * @since 4.0.0
     */
    function getFile();

    /**
     * Get the factory method.
     *
     * @return string The factory method name
     *
     * @api
     * @since 4.0.0
     */
    function getFactoryMethod();

    /**
     * Get the factory service id.
     *
     * @return string The factory service id
     *
     * @api
     * @since 4.0.0
     */
    function getFactoryService();

    /**
     * Get the methods to call after service initialization.
     *
     * @return array An array of method calls
     *
     * @api
     * @since 4.0.0
     */
    function getMethodCalls();
    
    /**
     * Get the properties for this definition.
     *
     * @return array An array or properties, which may be empty.
     *
     * @api
     * @since 4.0.0
     */
    function getProperties();

    /**
     * Get a tag by name.
     *
     * @param string $name The tag name
     *
     * @return array An array of attributes
     *
     * @api
     * @since 4.0.0
     */
    function getTag($name);
    
    /**
     * Get all tags.
     *
     * @return array An array of tags
     *
     * @api
     * @since 4.0.0
     */
    function getTags();
    
    /**
     * Check if the current definition has a given method to call after service initialization.
     *
     * @param string $method The method name to search for
     *
     * @return Boolean
     *
     * @api
     * @since 4.0.0
     */
    function hasMethodCall($method);

    /**
     * Check whether this definition has a tag with the given name
     *
     * @param string $name The name of the tag
     *
     * @return Boolean
     *
     * @api
     * @since 4.0.0
     */
    function hasTag($name);
    
    /**
     * Remove a method to call after service initialization.
     *
     * @param string $method The method name to remove
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function removeMethodCall($method);
    
    /**
     * Set a specific argument
     *
     * @param integer $index	The index of the argument being replaced
     * @param mixed   $argument	The value of the argument being replaced
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws OutOfBoundsException When the replaced argument does not exist
     *
     * @api
     * @since 4.0.0
     */
    function replaceArgument($index, $argument);

    /**
     * Set the arguments to pass to the service constructor/factory method.
     *
     * @param array $arguments An array of arguments
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setArguments(array $arguments);
    
    /**
     * Set the service class.
     *
     * @param string $class The service class
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setClass($class);

    /**
     * Set a configurator to call after the service is fully initialized.
     *
     * @param callable $callable A PHP callable
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setConfigurator($callable);

    /**
     * Sets the service that this service is decorating.
     *
     * @param null|string $id        The decorated service id, use null to remove decoration
     * @param null|string $renamedId The new decorated service id
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws InvalidArgumentException In case the decorated service id and the new decorated service id are equals.
     *
     * @api
     * @since 4.0.0
     */
    function setDecoratedService($id, $renamedId = null);

    /**
     * Set the name of the class that acts as a factory using the factory method,
     * which will be invoked statically.
     *
     * @param string $factoryClass The factory class name
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setFactoryClass($factoryClass);

    /**
     * Set the factory method able to create an instance of this class.
     *
     * @param string $factoryMethod The factory method name
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setFactoryMethod($factoryMethod);

    /**
     * Set the name of the service that acts as a factory using the factory method.
     *
     * @param string $factoryService The factory service id
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setFactoryService($factoryService);

    /**
     * Set a file to require before creating the service.
     *
     * @param string $file A full pathname to include
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setFile($file);
    
    /**
     * Set the methods to call after service initialization.
     *
     * @param array $calls An array of method calls
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    function setMethodCalls(array $calls = array());
    
    /**
     * Set properties on the service.
     *
     * @param array $properties An associative array of property names to their respective
     *                          values.
     *
     * @api
     * @since 4.0.0
     */
    function setProperties(array $properties);

    /**
     * Set a property on the service.
     *
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     *
     * @api
     * @since 4.0.0
     */
    function setProperty($name, $value);

    /**
     * Set tags for this definition
     *
     * @param array $tags
     *
     * @return tubepress_api_ioc_DefinitionInterface the current instance
     *
     * @api
     * @since 4.0.0
     */
    function setTags(array $tags);
}