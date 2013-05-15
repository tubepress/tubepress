<?php

class tubepress_impl_ioc_IconicDefinitionWrapper extends ehough_iconic_Definition
{
    /**
     * @var tubepress_api_ioc_DefinitionInterface
     */
    private $_delegate;

    public function __construct(tubepress_api_ioc_DefinitionInterface $tubePressDefinition)
    {
        $this->_delegate = $tubePressDefinition;

        parent::__construct($tubePressDefinition->getClass(), $tubePressDefinition->getArguments());

        $this->setClass($this->_delegate->getClass());
        $this->setArguments($this->_delegate->getArguments());
    }

    /**
     * Sets the name of the class that acts as a factory using the factory method,
     * which will be invoked statically.
     *
     * @param string $factoryClass The factory class name
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setFactoryClass($factoryClass)
    {
        $this->_delegate->setFactoryClass($factoryClass);

        return parent::setFactoryClass($factoryClass);
    }

    /**
     * Gets the factory class.
     *
     * @return string The factory class name
     *
     * @api
     */
    public function getFactoryClass()
    {
        return $this->_delegate->getFactoryClass();
    }

    /**
     * Sets the factory method able to create an instance of this class.
     *
     * @param string $factoryMethod The factory method name
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setFactoryMethod($factoryMethod)
    {
        $this->_delegate->setFactoryMethod($factoryMethod);

        return parent::setFactoryMethod($factoryMethod);
    }

    /**
     * Gets the factory method.
     *
     * @return string The factory method name
     *
     * @api
     */
    public function getFactoryMethod()
    {
        return $this->_delegate->getFactoryMethod();
    }

    /**
     * Sets the name of the service that acts as a factory using the factory method.
     *
     * @param string $factoryService The factory service id
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setFactoryService($factoryService)
    {
        $this->_delegate->setFactoryService($factoryService);

        return parent::setFactoryService($factoryService);
    }

    /**
     * Gets the factory service id.
     *
     * @return string The factory service id
     *
     * @api
     */
    public function getFactoryService()
    {
        return $this->_delegate->getFactoryService();
    }

    /**
     * Sets the service class.
     *
     * @param string $class The service class
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setClass($class)
    {
        $this->_delegate->setClass($class);

        return parent::setClass($class);
    }

    /**
     * Gets the service class.
     *
     * @return string The service class
     *
     * @api
     */
    public function getClass()
    {
        return $this->_delegate->getClass();
    }

    /**
     * Sets the arguments to pass to the service constructor/factory method.
     *
     * @param array $arguments An array of arguments
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setArguments(array $arguments)
    {
        $this->_delegate->setArguments($arguments);

        return parent::setArguments($arguments);
    }

    /**
     * @api
     */
    public function setProperties(array $properties)
    {
        $this->_delegate->setProperties($properties);

        return parent::setProperties($properties);
    }

    /**
     * @api
     */
    public function getProperties()
    {
        return $this->_delegate->getProperties();
    }

    /**
     * @api
     */
    public function setProperty($name, $value)
    {
        $this->_delegate->setProperty($name, $value);

        return parent::setProperty($name, $value);
    }

    /**
     * Adds an argument to pass to the service constructor/factory method.
     *
     * @param mixed $argument An argument
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function addArgument($argument)
    {
        $this->_delegate->addArgument($argument);

        return parent::addArgument($argument);
    }

    /**
     * Sets a specific argument
     *
     * @param integer $index
     * @param mixed   $argument
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @throws ehough_iconic_exception_OutOfBoundsException When the replaced argument does not exist
     *
     * @api
     */
    public function replaceArgument($index, $argument)
    {
        try {

            $this->_delegate->replaceArgument($index, $argument);

        } catch (OutOfBoundsException $e) {

            throw new ehough_iconic_exception_OutOfBoundsException($e->getMessage());
        }

        return parent::replaceArgument($index, $argument);
    }

    /**
     * Gets the arguments to pass to the service constructor/factory method.
     *
     * @return array The array of arguments
     *
     * @api
     */
    public function getArguments()
    {
        return $this->_delegate->getArguments();
    }

    /**
     * Gets an argument to pass to the service constructor/factory method.
     *
     * @param integer $index
     *
     * @return mixed The argument value
     *
     * @throws ehough_iconic_exception_OutOfBoundsException When the argument does not exist
     *
     * @api
     */
    public function getArgument($index)
    {
        try {

            return $this->_delegate->getArgument($index);

        } catch (OutOfBoundsException $e) {

            throw new ehough_iconic_exception_OutOfBoundsException($e->getMessage());
        }
    }

    /**
     * Sets the methods to call after service initialization.
     *
     * @param array $calls An array of method calls
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setMethodCalls(array $calls = array())
    {
        $this->_delegate->setMethodCalls($calls);

        return parent::setMethodCalls($calls);
    }

    /**
     * Adds a method to call after service initialization.
     *
     * @param string $method    The method name to call
     * @param array  $arguments An array of arguments to pass to the method call
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @throws ehough_iconic_exception_InvalidArgumentException on empty $method param
     *
     * @api
     */
    public function addMethodCall($method, array $arguments = array())
    {
        try {

            $this->_delegate->addMethodCall($method, $arguments);

        } catch (InvalidArgumentException $e) {

            throw new ehough_iconic_exception_InvalidArgumentException($e->getMessage());
        }

        return parent::addMethodCall($method, $arguments);
    }

    /**
     * Removes a method to call after service initialization.
     *
     * @param string $method The method name to remove
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function removeMethodCall($method)
    {
        $this->_delegate->removeMethodCall($method);

        return parent::removeMethodCall($method);
    }

    /**
     * Check if the current definition has a given method to call after service initialization.
     *
     * @param string $method The method name to search for
     *
     * @return Boolean
     *
     * @api
     */
    public function hasMethodCall($method)
    {
        return $this->_delegate->hasMethodCall($method);
    }

    /**
     * Gets the methods to call after service initialization.
     *
     * @return array An array of method calls
     *
     * @api
     */
    public function getMethodCalls()
    {
        return $this->_delegate->getMethodCalls();
    }

    /**
     * Sets tags for this definition
     *
     * @param array $tags
     *
     * @return ehough_iconic_Definition the current instance
     *
     * @api
     */
    public function setTags(array $tags)
    {
        $this->_delegate->setTags($tags);

        return parent::setTags($tags);
    }

    /**
     * Returns all tags.
     *
     * @return array An array of tags
     *
     * @api
     */
    public function getTags()
    {
        return $this->_delegate->getTags();
    }

    /**
     * Gets a tag by name.
     *
     * @param string $name The tag name
     *
     * @return array An array of attributes
     *
     * @api
     */
    public function getTag($name)
    {
        return $this->_delegate->getTag($name);
    }

    /**
     * Adds a tag for this definition.
     *
     * @param string $name       The tag name
     * @param array  $attributes An array of attributes
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function addTag($name, array $attributes = array())
    {
        $this->_delegate->addTag($name, $attributes);

        return parent::addTag($name, $attributes);
    }

    /**
     * Whether this definition has a tag with the given name
     *
     * @param string $name
     *
     * @return Boolean
     *
     * @api
     */
    public function hasTag($name)
    {
        return $this->_delegate->hasTag($name);
    }

    /**
     * Clears all tags for a given name.
     *
     * @param string $name The tag name
     *
     * @return ehough_iconic_Definition
     */
    public function clearTag($name)
    {
       $this->_delegate->clearTag($name);

        return parent::clearTag($name);
    }

    /**
     * Clears the tags for this definition.
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function clearTags()
    {
        $this->_delegate->clearTags();

        return parent::clearTags();
    }

    /**
     * Sets a file to require before creating the service.
     *
     * @param string $file A full pathname to include
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setFile($file)
    {
        $this->_delegate->setFile($file);

        return parent::setFile($file);
    }

    /**
     * Gets the file to require before creating the service.
     *
     * @return string The full pathname to include
     *
     * @api
     */
    public function getFile()
    {
        return $this->_delegate->getFile();
    }

    /**
     * Sets a configurator to call after the service is fully initialized.
     *
     * @param callable $callable A PHP callable
     *
     * @return ehough_iconic_Definition The current instance
     *
     * @api
     */
    public function setConfigurator($callable)
    {
        $this->_delegate->setConfigurator($callable);

        return parent::setConfigurator($callable);
    }

    /**
     * Gets the configurator to call after the service is fully initialized.
     *
     * @return callable The PHP callable to call
     *
     * @api
     */
    public function getConfigurator()
    {
        return $this->_delegate->getConfigurator();
    }

    /**
     * @return tubepress_api_ioc_DefinitionInterface
     */
    public function getTubePressDefinition()
    {
        return $this->_delegate;
    }
}