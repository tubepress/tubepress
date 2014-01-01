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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
class tubepress_impl_ioc_IconicContainer implements tubepress_api_ioc_ContainerInterface, tubepress_api_ioc_CompilerPassInterface
{
    /**
     * @var ehough_iconic_Container
     */
    private $_delegate;

    /**
     * @var array An array of tubepress_api_ioc_ContainerExtensionInterface instances
     */
    private $_extensions = array();

    /**
     * @var array An array of tubepress_api_ioc_CompilerPassInterface instances
     */
    private $_compilerPasses = array();

    /**
     * @var bool
     */
    private $_isFrozen = false;

    public function __construct(ehough_iconic_parameterbag_ParameterBagInterface $params = null)
    {
        $this->_delegate = new ehough_iconic_ContainerBuilder($params);

        $this->_compilerPasses[] = $this;

        /**
         * Turn off resource loading.
         */
        $this->_delegate->setResourceTracking(false);
    }

    public function compile()
    {
        /**
         * @var $pass tubepress_api_ioc_CompilerPassInterface
         */
        foreach ($this->_compilerPasses as $pass) {

            $pass->process($this);
        }

        $this->_delegate->getParameterBag()->resolve();
        $this->_isFrozen = true;
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_ContainerExtensionInterface $extension
     */
    public function registerExtension(tubepress_api_ioc_ContainerExtensionInterface $extension)
    {
        $this->_extensions[] = $extension;
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_CompilerPassInterface $pass
     *
     * @return tubepress_api_ioc_ContainerInterface The current instance.
     */
    public function addCompilerPass(tubepress_api_ioc_CompilerPassInterface $pass)
    {
        $this->_compilerPasses[] = $pass;
    }

    /**
     * @return ehough_iconic_ContainerBuilder
     */
    public function getDelegateIconicContainerBuilder()
    {
        return $this->_delegate;
    }

    public function setDelegateIconicContainerBuilder(ehough_iconic_ContainerBuilder $builder)
    {
        $this->_delegate = $builder;
    }

    public function isFrozen()
    {
        return $this->_isFrozen;
    }

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
    function addDefinitions(array $definitions)
    {
        if ($this->isFrozen()) {

            throw new BadMethodCallException('Cannot set definitions on a frozen container');

        }
        $iconicDefinitions = array_map(array($this, '_callbackConvertToIconicDefinition'), $definitions);

        $this->_delegate->addDefinitions($iconicDefinitions);
    }

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
    function findTaggedServiceIds($name)
    {
        return $this->_delegate->findTaggedServiceIds($name);
    }

    /**
     * Returns all tags the defined services use.
     *
     * @return array An array of tags
     *
     * @api
     * @since 3.1.0
     */
    function findTags()
    {
        return $this->_delegate->findTags();
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     *
     * @return object The associated service, or null if not defined.
     *
     * @throws RuntimeException If there was a problem retrieving the service.
     *
     * @api
     * @since 3.1.0
     */
    public function get($id)
    {
        if (!$this->_delegate->has($id)) {

            return null;
        }

        try {

            return $this->_delegate->get($id);

        } catch (Exception $e) {

            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Gets a service definition.
     *
     * @param string $id The service identifier
     *
     * @return tubepress_api_ioc_DefinitionInterface A tubepress_api_ioc_DefinitionInterface instance, or null if the
     *                                               service does not exist.
     *
     * @api
     * @since                                        3.1.0
     */
    public function getDefinition($id)
    {
        try {

            /**
             * @var $fromDelegate tubepress_impl_ioc_IconicDefinitionWrapper
             */
            $fromDelegate = $this->_delegate->getDefinition($id);

            return $this->_callbackConvertToTubePressDefinition($fromDelegate);

        } catch (ehough_iconic_exception_InvalidArgumentException $e) {

            return null;
        }
    }

    /**
     * Gets all service definitions.
     *
     * @return tubepress_api_ioc_DefinitionInterface[] An array of tubepress_api_ioc_DefinitionInterface instances
     *
     * @api
     * @since 3.1.0
     */
    public function getDefinitions()
    {
        $definitionsFromDelegate = $this->_delegate->getDefinitions();

        return array_map(array($this, '_callbackConvertToTubePressDefinition'), $definitionsFromDelegate);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     *
     * @api
     * @since 3.1.0
     */
    public function getParameter($name)
    {
        try {

            return $this->_delegate->getParameter($name);

        } catch (Exception $e) {

            throw new InvalidArgumentException('Parameter ' . $name . ' not found');
        }
    }

    /**
     * Gets all service ids.
     *
     * @return array An array of all defined service ids
     *
     * @api
     * @since 3.1.0
     */
    public function getServiceIds()
    {
        return $this->_delegate->getServiceIds();
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return Boolean true if the service is defined, false otherwise
     *
     * @api
     * @since 3.1.0
     */
    public function has($id)
    {
        return $this->_delegate->has($id);
    }

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
    public function hasDefinition($id)
    {
        return $this->_delegate->hasDefinition($id);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return Boolean The presence of parameter in container
     *
     * @api
     * @since 3.1.0
     */
    public function hasParameter($name)
    {
        return $this->_delegate->hasParameter($name);
    }

    /**
     * Check for whether or not a service has been initialized.
     *
     * @param string $id
     *
     * @return Boolean true if the service has been initialized, false otherwise
     *
     * @api
     * @since 3.1.0
     */
    public function initialized($id)
    {
        return $this->_delegate->initialized($id);
    }

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
    public function register($id, $class = null)
    {
        return $this->setDefinition(strtolower($id), new tubepress_impl_ioc_Definition($class));
    }

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
    public function removeDefinition($id)
    {
        $this->_delegate->removeDefinition($id);
    }

    /**
     * Sets a service.
     *
     * @param string $id      The service identifier
     * @param object $service The service instance
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function set($id, $service)
    {
        if ($this->isFrozen()) {

            throw new BadMethodCallException('Cannot set a service on a frozen container');
        }

        $this->_delegate->set($id, $service);
    }

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
    public function setDefinition($id, tubepress_api_ioc_DefinitionInterface $definition)
    {
        if ($this->_isFrozen) {

            throw new BadMethodCallException('Setting a definition on a frozen container is not allowed');
        }

        $wrapped = new tubepress_impl_ioc_IconicDefinitionWrapper($definition);

        try {

            /**
             * @var $fromParent tubepress_impl_ioc_IconicDefinitionWrapper
             */
            $fromParent = $this->_delegate->setDefinition($id, $wrapped);

            return $fromParent->getTubePressDefinition();

        } catch (ehough_iconic_exception_BadMethodCallException $e) {

            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * Sets the service definitions.
     *
     * @param tubepress_api_ioc_DefinitionInterface[] $definitions An array of service definitions
     *
     * @api
     * @since 3.1.0
     */
    public function setDefinitions(array $definitions)
    {
        if ($this->isFrozen()) {

            throw new BadMethodCallException('Cannot set definitions on a frozen container');
        }

        $iconicDefinitions = array_map(array($this, '_callbackConvertToIconicDefinition'), $definitions);

        $this->_delegate->setDefinitions($iconicDefinitions);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function setParameter($name, $value)
    {
        if ($this->isFrozen()) {

            throw new LogicException('Cannot set a parameter on a frozen container');
        }

        $this->_delegate->setParameter($name, $value);
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param tubepress_api_ioc_ContainerInterface $self
     *
     * @api
     */
    public function process(tubepress_api_ioc_ContainerInterface $self)
    {
        $parameters = $this->_delegate->getParameterBag()->all();

        /**
         * These will all be tubepress_impl_ioc_IconicDefinitionWrapper instances
         */
        $definitions = $self->getDefinitions();

        /**
         * @var $extension tubepress_api_ioc_ContainerExtensionInterface
         */
        foreach ($this->_extensions as $extension) {

            $tmpContainer = new tubepress_impl_ioc_IconicContainer($this->_delegate->getParameterBag());

            $extension->load($tmpContainer);

            $this->merge($tmpContainer);
        }

        $self->addDefinitions($definitions);
        $this->_delegate->getParameterBag()->add($parameters);
    }

    /**
     * @internal
     */
    public function getParameterBag()
    {
        return $this->_delegate->getParameterBag();
    }

    /**
     * @internal
     */
    private function merge(tubepress_impl_ioc_IconicContainer $container)
    {
        if ($this->_isFrozen) {

            throw new BadMethodCallException('Cannot merge on a frozen container.');
        }

        $this->addDefinitions($container->getDefinitions());
        $this->getParameterBag()->add($container->getParameterBag()->all());
    }

    /**
     * @internal
     */
    public function _callbackConvertToIconicDefinition($definition)
    {
        if ($definition instanceof tubepress_impl_ioc_IconicDefinitionWrapper) {

            return $definition;
        }

        if (!($definition instanceof tubepress_api_ioc_DefinitionInterface)) {

            throw new InvalidArgumentException('Can only add tubepress_api_ioc_DefinitionInterface instances to the ' .
                'container. You supplied an instance of ' . get_class($definition));
        }

        return new tubepress_impl_ioc_IconicDefinitionWrapper($definition);
    }

    /**
     * @internal
     */
    public function _callbackConvertToTubePressDefinition($definition)
    {
        if (!($definition instanceof tubepress_impl_ioc_IconicDefinitionWrapper)) {

            throw new InvalidArgumentException('A non-tubepress_impl_ioc_IconicDefinitionWrapper made it\'s way into the container.');
        }

        return $definition->getTubePressDefinition();
    }
}