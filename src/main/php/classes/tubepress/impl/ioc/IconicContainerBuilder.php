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
class tubepress_impl_ioc_IconicContainerBuilder implements tubepress_api_ioc_ContainerBuilderInterface, tubepress_api_ioc_CompilerPassInterface
{
    /**
     * @var ehough_iconic_ContainerBuilder
     */
    private $_delegateContainerBuilder;

    /**
     * @var array An array of tubepress_api_ioc_ContainerExtensionInterface instances
     */
    private $_tubePressContainerExtensions = array();

    /**
     * @var array An array of tubepress_api_ioc_CompilerPassInterface instances
     */
    private $_tubePressCompilerPasses = array();

    public function __construct(ehough_iconic_parameterbag_ParameterBagInterface $params = null)
    {
        $this->_delegateContainerBuilder = new ehough_iconic_ContainerBuilder($params);

        /**
         * Turn off resource tracking.
         */
        $this->_delegateContainerBuilder->setResourceTracking(false);

        /**
         * Add ourself as the first compiler pass.
         */
        $this->_tubePressCompilerPasses[] = $this;
    }

    /**
     * This is necessary so the boostrapper can dump the container to PHP.
     *
     * @return ehough_iconic_ContainerBuilder
     */
    public function getDelegateIconicContainerBuilder()
    {
        return $this->_delegateContainerBuilder;
    }

    public function compile()
    {
        /**
         * @var $pass tubepress_api_ioc_CompilerPassInterface
         */
        foreach ($this->_tubePressCompilerPasses as $pass) {

            $pass->process($this);
        }

        $this->_delegateContainerBuilder->compile();
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_ContainerExtensionInterface $extension
     */
    public function registerExtension(tubepress_api_ioc_ContainerExtensionInterface $extension)
    {
        $this->_tubePressContainerExtensions[] = $extension;
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_CompilerPassInterface $pass
     *
     * @return void
     */
    public function addCompilerPass(tubepress_api_ioc_CompilerPassInterface $pass)
    {
        $this->_tubePressCompilerPasses[] = $pass;
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
    public function addDefinitions(array $definitions)
    {
        foreach ($definitions as $definition) {

            $this->_verifyIsIconicDefinition($definition);
        }

        $this->_delegateContainerBuilder->addDefinitions($definitions);
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
        return $this->_delegateContainerBuilder->findTaggedServiceIds($name);
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
        return $this->_delegateContainerBuilder->findTags();
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     *
     * @return object The associated service, or null if not defined.
     *
     * @api
     * @since 3.1.0
     */
    public function get($id)
    {
        return $this->_delegateContainerBuilder->get($id, ehough_iconic_ContainerInterface::NULL_ON_INVALID_REFERENCE);
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

            return $this->_delegateContainerBuilder->getDefinition($id);

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
        return $this->_delegateContainerBuilder->getDefinitions();
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

            return $this->_delegateContainerBuilder->getParameter($name);

        } catch (Exception $e) {

            throw new InvalidArgumentException('Parameter ' . $name . ' not found');
        }
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
        return $this->_delegateContainerBuilder->has($id);
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
        return $this->_delegateContainerBuilder->hasDefinition($id);
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
        return $this->_delegateContainerBuilder->hasParameter($name);
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
        $this->_delegateContainerBuilder->removeDefinition($id);
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
        $this->_delegateContainerBuilder->set($id, $service);
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
        $this->_verifyIsIconicDefinition($definition);

        try {

            return $this->_delegateContainerBuilder->setDefinition($id, $definition);

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
        foreach ($definitions as $definition) {

            $this->_verifyIsIconicDefinition($definition);
        }

        $this->_delegateContainerBuilder->setDefinitions($definitions);
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
        $this->_delegateContainerBuilder->setParameter($name, $value);
    }

    /**
     * Based heavily on ehough_iconic_compiler_MergeExtensionConfigurationPass.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $self
     *
     * @api
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $self)
    {
        $parameters = $this->_delegateContainerBuilder->getParameterBag()->all();

        /**
         * These will all be tubepress_impl_ioc_Definition instances
         */
        $definitions = $self->getDefinitions();

        /**
         * @var $extension tubepress_api_ioc_ContainerExtensionInterface
         */
        foreach ($this->_tubePressContainerExtensions as $extension) {

            $tmpContainer = new tubepress_impl_ioc_IconicContainerBuilder($this->_delegateContainerBuilder->getParameterBag());

            $extension->load($tmpContainer);

            $this->merge($tmpContainer);
        }

        $self->addDefinitions($definitions);

        $this->_delegateContainerBuilder->getParameterBag()->add($parameters);
    }

    /**
     * @internal
     */
    private function getParameterBag()
    {
        return $this->_delegateContainerBuilder->getParameterBag();
    }

    /**
     * @internal
     */
    private function merge(tubepress_impl_ioc_IconicContainerBuilder $containerBuilder)
    {
        $this->addDefinitions($containerBuilder->getDefinitions());
        $this->getParameterBag()->add($containerBuilder->getParameterBag()->all());
    }

    private function _verifyIsIconicDefinition($candidate)
    {
        if (!($candidate instanceof ehough_iconic_Definition)) {

            throw new InvalidArgumentException('This container implementation only deals with ehough_iconic_Definition instances');
        }
    }
}