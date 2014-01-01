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

interface tubepress_api_ioc_DefinitionInterface
{
    /**
     * Adds an argument to pass to the service constructor/factory method.
     *
     * @param mixed $argument An argument
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function addArgument($argument);
    
    /**
     * Adds a method to call after service initialization.
     *
     * @param string $method    The method name to call
     * @param array  $arguments An array of arguments to pass to the method call
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws InvalidArgumentException on empty $method param
     *
     * @api
     * @since 3.1.0
     */
    function addMethodCall($method, array $arguments = array());

    /**
     * Adds a tag for this definition.
     *
     * @param string $name       The tag name
     * @param array  $attributes An array of attributes
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function addTag($name, array $attributes = array());

    /**
     * Clears all tags for a given name.
     *
     * @param string $name The tag name
     *
     * @return tubepress_api_ioc_DefinitionInterface
     *
     * @api
     * @since 3.1.0
     */
    function clearTag($name);

    /**
     * Clears the tags for this definition.
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function clearTags();
    
    /**
     * Gets an argument to pass to the service constructor/factory method.
     *
     * @param integer $index
     *
     * @return mixed The argument value
     *
     * @throws OutOfBoundsException When the argument does not exist
     *
     * @api
     * @since 3.1.0
     */
    function getArgument($index);

    /**
     * Gets the arguments to pass to the service constructor/factory method.
     *
     * @return array The array of arguments
     *
     * @api
     * @since 3.1.0
     */
    function getArguments();

    /**
     * Gets the service class.
     *
     * @return string The service class
     *
     * @api
     * @since 3.1.0
     */
    function getClass();
    
    /**
     * Gets the configurator to call after the service is fully initialized.
     *
     * @return callable The PHP callable to call
     *
     * @api
     * @since 3.1.0
     */
    function getConfigurator();

    /**
     * Gets the factory class.
     *
     * @return string The factory class name
     *
     * @api
     * @since 3.1.0
     */
    function getFactoryClass();

    /**
     * Gets the file to require before creating the service.
     *
     * @return string The full pathname to include
     *
     * @api
     * @since 3.1.0
     */
    function getFile();

    /**
     * Gets the factory method.
     *
     * @return string The factory method name
     *
     * @api
     * @since 3.1.0
     */
    function getFactoryMethod();

    /**
     * Gets the factory service id.
     *
     * @return string The factory service id
     *
     * @api
     * @since 3.1.0
     */
    function getFactoryService();

    /**
     * Gets the methods to call after service initialization.
     *
     * @return array An array of method calls
     *
     * @api
     * @since 3.1.0
     */
    function getMethodCalls();
    
    /**
     * @api
     * @since 3.1.0
     */
    function getProperties();

    /**
     * Gets a tag by name.
     *
     * @param string $name The tag name
     *
     * @return array An array of attributes
     *
     * @api
     * @since 3.1.0
     */
    function getTag($name);
    
    /**
     * Returns all tags.
     *
     * @return array An array of tags
     *
     * @api
     * @since 3.1.0
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
     * @since 3.1.0
     */
    function hasMethodCall($method);


    /**
     * Whether this definition has a tag with the given name
     *
     * @param string $name
     *
     * @return Boolean
     *
     * @api
     * @since 3.1.0
     */
    function hasTag($name);
    
    /**
     * Removes a method to call after service initialization.
     *
     * @param string $method The method name to remove
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function removeMethodCall($method);
    
    /**
     * Sets a specific argument
     *
     * @param integer $index
     * @param mixed   $argument
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws OutOfBoundsException When the replaced argument does not exist
     *
     * @api
     * @since 3.1.0
     */
    function replaceArgument($index, $argument);

    /**
     * Sets the arguments to pass to the service constructor/factory method.
     *
     * @param array $arguments An array of arguments
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setArguments(array $arguments);
    
    /**
     * Sets the service class.
     *
     * @param string $class The service class
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setClass($class);


    /**
     * Sets a configurator to call after the service is fully initialized.
     *
     * @param callable $callable A PHP callable
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setConfigurator($callable);
    
    /**
     * Sets the name of the class that acts as a factory using the factory method,
     * which will be invoked statically.
     *
     * @param string $factoryClass The factory class name
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setFactoryClass($factoryClass);

    /**
     * Sets the factory method able to create an instance of this class.
     *
     * @param string $factoryMethod The factory method name
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setFactoryMethod($factoryMethod);

    /**
     * Sets the name of the service that acts as a factory using the factory method.
     *
     * @param string $factoryService The factory service id
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setFactoryService($factoryService);

    /**
     * Sets a file to require before creating the service.
     *
     * @param string $file A full pathname to include
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setFile($file);
    
    /**
     * Sets the methods to call after service initialization.
     *
     * @param array $calls An array of method calls
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 3.1.0
     */
    function setMethodCalls(array $calls = array());
    
    /**
     * @api
     * @since 3.1.0
     */
    function setProperties(array $properties);

    /**
     * @api
     * @since 3.1.0
     */
    function setProperty($name, $value);

    /**
     * Sets tags for this definition
     *
     * @param array $tags
     *
     * @return tubepress_api_ioc_DefinitionInterface the current instance
     *
     * @api
     * @since 3.1.0
     */
    function setTags(array $tags);
}