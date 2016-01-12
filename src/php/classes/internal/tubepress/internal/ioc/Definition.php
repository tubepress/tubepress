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

class tubepress_internal_ioc_Definition implements tubepress_api_ioc_DefinitionInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Definition
     */
    private $_underlyingSymfonyDefinition;

    public function __construct($class = null, array $arguments = array())
    {
        $cleanedArgs = $this->convertToSymfonyReferenceIfNecessary($arguments);

        $this->_underlyingSymfonyDefinition = new \Symfony\Component\DependencyInjection\Definition($class, $cleanedArgs);
    }

    public static function fromSymfonyDefinition(\Symfony\Component\DependencyInjection\Definition $definition)
    {
        $toReturn = new self();

        $toReturn->setSymfonyDefinition($definition);

        return $toReturn;
    }

    private function setSymfonyDefinition(\Symfony\Component\DependencyInjection\Definition $definition)
    {
        $this->_underlyingSymfonyDefinition = $definition;
    }

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
    public function addArgument($argument)
    {
        $cleaned = $this->convertToSymfonyReferenceIfNecessary($argument);

        $this->_underlyingSymfonyDefinition->addArgument($cleaned);

        return $this;
    }

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
    public function setArguments(array $arguments)
    {
        $cleaned = array_map(array($this, 'convertToSymfonyReferenceIfNecessary'), $arguments);

        $this->_underlyingSymfonyDefinition->setArguments($cleaned);

        return $this;
    }

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
     * @since 4.0.0
     */
    public function replaceArgument($index, $argument)
    {
        $cleaned = $this->convertToSymfonyReferenceIfNecessary($argument);

        try {

            $this->_underlyingSymfonyDefinition->replaceArgument($index, $cleaned);

        } catch (\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException $e) {

            throw new OutOfBoundsException($e);
        }

        return $this;
    }


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
     * @since 4.0.0
     */
    public function getArgument($index)
    {
        try {

            $symfonyArgument = $this->_underlyingSymfonyDefinition->getArgument($index);

            return $this->convertToTubePressReferenceIfNecessary($symfonyArgument);

        } catch (\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException $e) {

            throw new OutOfBoundsException($e);
        }
    }

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
     * @since 4.0.0
     */
    public function addMethodCall($method, array $arguments = array())
    {
        try {

            $cleaned = $this->convertToSymfonyReferenceIfNecessary($arguments);

            $this->_underlyingSymfonyDefinition->addMethodCall($method, $cleaned);

        } catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $e) {

            throw new InvalidArgumentException($e);
        }

        return $this;
    }

    public function convertToSymfonyReferenceIfNecessary($candidate)
    {
        return $this->_convertReferences($candidate, 'tubepress_api_ioc_Reference', 'Symfony\Component\DependencyInjection\Reference');
    }

    public function convertToTubePressReferenceIfNecessary($candidate)
    {
        return $this->_convertReferences($candidate, 'Symfony\Component\DependencyInjection\Reference', 'tubepress_api_ioc_Reference');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    public function getUnderlyingSymfonyDefinition()
    {
        return $this->_underlyingSymfonyDefinition;
    }

    private function _convertReferences($candidate, $from, $to)
    {
        if ($candidate instanceof $from) {

            return new $to("$candidate");
        }

        if (is_array($candidate)) {

            foreach ($candidate as $name => $value) {

                $candidate[$name] = $this->_convertReferences($candidate[$name], $from, $to);
            }
        }

        return $candidate;
    }

    /**
     * Add a tag for this definition.
     *
     * @param string $name The tag name
     * @param array $attributes An array of attributes
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    public function addTag($name, array $attributes = array())
    {
        $this->_underlyingSymfonyDefinition->addTag($name, $attributes);

        return $this;
    }

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
    public function clearTag($name)
    {
        $this->_underlyingSymfonyDefinition->clearTag($name);

        return $this;
    }

    /**
     * Clear the tags for this definition.
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @api
     * @since 4.0.0
     */
    public function clearTags()
    {
        $this->_underlyingSymfonyDefinition->clearTags();

        return $this;
    }

    /**
     * Get the arguments to pass to the service constructor/factory method.
     *
     * @return array The array of arguments
     *
     * @api
     * @since 4.0.0
     */
    public function getArguments()
    {
        $symfonyArguments = $this->_underlyingSymfonyDefinition->getArguments();

        return $this->convertToTubePressReferenceIfNecessary($symfonyArguments);
    }

    /**
     * Get the service class.
     *
     * @return string The service class
     *
     * @api
     * @since 4.0.0
     */
    public function getClass()
    {
        return $this->_underlyingSymfonyDefinition->getClass();
    }

    /**
     * Get the configurator to call after the service is fully initialized.
     *
     * @return callable The PHP callable to call
     *
     * @api
     * @since 4.0.0
     */
    public function getConfigurator()
    {
        return $this->_underlyingSymfonyDefinition->getConfigurator();
    }

    /**
     * Gets the service that decorates this service.
     *
     * @return null|array An array composed of the decorated service id and the new id for it, null if no service is decorated
     *
     * @api
     * @since 4.0.0
     */
    public function getDecoratedService()
    {
        return $this->_underlyingSymfonyDefinition->getDecoratedService();
    }

    /**
     * Get the factory class.
     *
     * @return string The factory class name
     *
     * @api
     * @since 4.0.0
     */
    public function getFactoryClass()
    {
        return $this->_underlyingSymfonyDefinition->getFactoryClass();
    }

    /**
     * Get the file to require before creating the service.
     *
     * @return string The full pathname to include
     *
     * @api
     * @since 4.0.0
     */
    public function getFile()
    {
        return $this->_underlyingSymfonyDefinition->getFile();
    }

    /**
     * Get the factory method.
     *
     * @return string The factory method name
     *
     * @api
     * @since 4.0.0
     */
    public function getFactoryMethod()
    {
        return $this->_underlyingSymfonyDefinition->getFactoryMethod();
    }

    /**
     * Get the factory service id.
     *
     * @return string The factory service id
     *
     * @api
     * @since 4.0.0
     */
    public function getFactoryService()
    {
        return $this->_underlyingSymfonyDefinition->getFactoryService();
    }

    /**
     * Get the methods to call after service initialization.
     *
     * @return array An array of method calls
     *
     * @api
     * @since 4.0.0
     */
    public function getMethodCalls()
    {
        $symfonyMethodCalls = $this->_underlyingSymfonyDefinition->getMethodCalls();

        return $this->convertToTubePressReferenceIfNecessary($symfonyMethodCalls);
    }

    /**
     * Get the properties for this definition.
     *
     * @return array An array or properties, which may be empty.
     *
     * @api
     * @since 4.0.0
     */
    public function getProperties()
    {
        return $this->_underlyingSymfonyDefinition->getProperties();
    }

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
    public function getTag($name)
    {
        return $this->_underlyingSymfonyDefinition->getTag($name);
    }

    /**
     * Get all tags.
     *
     * @return array An array of tags
     *
     * @api
     * @since 4.0.0
     */
    public function getTags()
    {
        return $this->_underlyingSymfonyDefinition->getTags();
    }

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
    public function hasMethodCall($method)
    {
        return $this->_underlyingSymfonyDefinition->hasMethodCall($method);
    }

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
    public function hasTag($name)
    {
        return $this->_underlyingSymfonyDefinition->hasTag($name);
    }

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
    public function removeMethodCall($method)
    {
        $this->_underlyingSymfonyDefinition->removeMethodCall($method);

        return $this;
    }

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
    public function setClass($class)
    {
        $this->_underlyingSymfonyDefinition->setClass($class);

        return $this;
    }

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
    public function setConfigurator($callable)
    {
        $this->_underlyingSymfonyDefinition->setConfigurator($callable);

        return $this;
    }

    /**
     * Sets the service that this service is decorating.
     *
     * @param null|string $id The decorated service id, use null to remove decoration
     * @param null|string $renamedId The new decorated service id
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws InvalidArgumentException In case the decorated service id and the new decorated service id are equals.
     *
     * @api
     * @since 4.0.0
     */
    public function setDecoratedService($id, $renamedId = null)
    {
        $this->_underlyingSymfonyDefinition->setDecoratedService($id, $renamedId);

        return $this;
    }

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
    public function setFactoryClass($factoryClass)
    {
        $this->_underlyingSymfonyDefinition->setFactoryClass($factoryClass);

        return $this;
    }

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
    public function setFactoryMethod($factoryMethod)
    {
        $this->_underlyingSymfonyDefinition->setFactoryMethod($factoryMethod);

        return $this;
    }

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
    public function setFactoryService($factoryService)
    {
        $this->_underlyingSymfonyDefinition->setFactoryService($factoryService);

        return $this;
    }

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
    public function setFile($file)
    {
        $this->_underlyingSymfonyDefinition->setFile($file);

        return $this;
    }

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
    public function setMethodCalls(array $calls = array())
    {
        $cleaned = $this->convertToSymfonyReferenceIfNecessary($calls);

        $this->_underlyingSymfonyDefinition->setMethodCalls($cleaned);

        return $this;
    }

    /**
     * Set properties on the service.
     *
     * @param array $properties An associative array of property names to their respective
     *                          values.
     *
     * @api
     * @since 4.0.0
     */
    public function setProperties(array $properties)
    {
        $this->_underlyingSymfonyDefinition->setProperties($properties);

        return $this;
    }

    /**
     * Set a property on the service.
     *
     * @param string $name The property name.
     * @param mixed $value The property value.
     *
     * @api
     * @since 4.0.0
     */
    public function setProperty($name, $value)
    {
        $this->_underlyingSymfonyDefinition->setProperty($name, $value);

        return $this;
    }

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
    public function setTags(array $tags)
    {
        $this->_underlyingSymfonyDefinition->setTags($tags);

        return $this;
    }
}