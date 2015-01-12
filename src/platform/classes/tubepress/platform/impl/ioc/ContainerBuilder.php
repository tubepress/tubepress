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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
class tubepress_platform_impl_ioc_ContainerBuilder extends tubepress_platform_impl_ioc_Container implements tubepress_platform_api_ioc_ContainerBuilderInterface, tubepress_platform_api_ioc_CompilerPassInterface
{
    /**
     * @var ehough_iconic_ContainerBuilder
     */
    private $_delegateContainerBuilder;

    /**
     * @var array An array of tubepress_platform_api_ioc_ContainerExtensionInterface instances
     */
    private $_tubePressContainerExtensions = array();

    /**
     * @var array An array of tubepress_platform_api_ioc_CompilerPassInterface instances
     */
    private $_tubePressCompilerPasses = array();

    public function __construct()
    {
        $this->_delegateContainerBuilder = new ehough_iconic_ContainerBuilder();

        parent::__construct($this->_delegateContainerBuilder);

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
    public function getDelegateContainerBuilder()
    {
        return $this->_delegateContainerBuilder;
    }

    public function compile()
    {
        /**
         * @var $pass tubepress_platform_api_ioc_CompilerPassInterface
         */
        foreach ($this->_tubePressCompilerPasses as $pass) {

            $pass->process($this);
        }

        $compilerConfig = $this->_delegateContainerBuilder->getCompilerPassConfig();
        $compilerConfig->setRemovingPasses(array(
            new ehough_iconic_compiler_RemovePrivateAliasesPass(),
            new ehough_iconic_compiler_RemoveAbstractDefinitionsPass(),
            new ehough_iconic_compiler_ReplaceAliasByActualDefinitionPass(),
            new ehough_iconic_compiler_RepeatedPass(array(
                new ehough_iconic_compiler_AnalyzeServiceReferencesPass(),
                new ehough_iconic_compiler_RemoveUnusedDefinitionsPass(),
            )),
            new ehough_iconic_compiler_CheckExceptionOnInvalidReferenceBehaviorPass(),
        ));

        $this->_delegateContainerBuilder->compile();
    }

    /**
     * @internal
     *
     * @param tubepress_platform_api_ioc_ContainerExtensionInterface $extension
     */
    public function registerExtension(tubepress_platform_api_ioc_ContainerExtensionInterface $extension)
    {
        $this->_tubePressContainerExtensions[] = $extension;
    }

    /**
     * @internal
     *
     * @param tubepress_platform_api_ioc_CompilerPassInterface $pass
     *
     * @return void
     */
    public function addCompilerPass(tubepress_platform_api_ioc_CompilerPassInterface $pass)
    {
        $this->_tubePressCompilerPasses[] = $pass;
    }

    /**
     * Adds the service definitions.
     *
     * @param tubepress_platform_api_ioc_DefinitionInterface[] $definitions An array of service definitions
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function addDefinitions(array $definitions)
    {
        $cleaned = array_map(array($this, 'toIconicDefinition'), $definitions);

        $this->_delegateContainerBuilder->addDefinitions($cleaned);
    }

    /**
     * Returns service ids for a given tag.
     *
     * @param string $name The tag name
     *
     * @return array An array of tags
     *
     * @api
     * @since 4.0.0
     */
    public function findTaggedServiceIds($name)
    {
        return $this->_delegateContainerBuilder->findTaggedServiceIds($name);
    }

    /**
     * Returns all tags the defined services use.
     *
     * @return array An array of tags
     *
     * @api
     * @since 4.0.0
     */
    public function findTags()
    {
        return $this->_delegateContainerBuilder->findTags();
    }

    /**
     * Gets a service definition.
     *
     * @param string $id The service identifier
     *
     * @return tubepress_platform_api_ioc_DefinitionInterface A tubepress_platform_api_ioc_DefinitionInterface instance, or null if the
     *                                               service does not exist.
     *
     * @api
     * @since                                        3.1.0
     */
    public function getDefinition($id)
    {
        try {

            $iconicDefinition = $this->_delegateContainerBuilder->getDefinition($id);

            return $this->toTubePressDefinition($iconicDefinition);

        } catch (ehough_iconic_exception_InvalidArgumentException $e) {

            return null;
        }
    }

    /**
     * Gets all service definitions.
     *
     * @return tubepress_platform_api_ioc_DefinitionInterface[] An array of tubepress_platform_api_ioc_DefinitionInterface instances
     *
     * @api
     * @since 4.0.0
     */
    public function getDefinitions()
    {
        $iconicDefinitions = $this->_delegateContainerBuilder->getDefinitions();

        return array_map(array($this, 'toTubePressDefinition'), $iconicDefinitions);
    }

    /**
     * Returns true if a service definition exists under the given identifier.
     *
     * @param string $id The service identifier
     *
     * @return Boolean true if the service definition exists, false otherwise
     *
     * @api
     * @since 4.0.0
     */
    public function hasDefinition($id)
    {
        return $this->_delegateContainerBuilder->hasDefinition($id);
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
     * @return tubepress_platform_api_ioc_DefinitionInterface A tubepress_platform_api_ioc_DefinitionInterface instance
     *
     * @api
     * @since 4.0.0
     */
    public function register($id, $class = null)
    {
        return $this->setDefinition(strtolower($id), new tubepress_platform_impl_ioc_Definition($class));
    }

    /**
     * Removes a service definition.
     *
     * @param string $id The service identifier
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function removeDefinition($id)
    {
        $this->_delegateContainerBuilder->removeDefinition($id);
    }

    /**
     * Sets a service definition.
     *
     * @param string                                $id         The service identifier
     * @param tubepress_platform_api_ioc_DefinitionInterface $definition A tubepress_platform_api_ioc_DefinitionInterface instance
     *
     * @return tubepress_platform_api_ioc_DefinitionInterface the service definition
     *
     * @throws BadMethodCallException When this ContainerBuilder is frozen
     *
     * @api
     * @since 4.0.0
     */
    public function setDefinition($id, tubepress_platform_api_ioc_DefinitionInterface $definition)
    {
        try {

            $iconicDefinition = $this->toIconicDefinition($definition);
            $added            = $this->_delegateContainerBuilder->setDefinition($id, $iconicDefinition);

            return $this->toTubePressDefinition($added);

        } catch (ehough_iconic_exception_BadMethodCallException $e) {

            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * Sets the service definitions.
     *
     * @param tubepress_platform_api_ioc_DefinitionInterface[] $definitions An array of service definitions
     *
     * @api
     * @since 4.0.0
     */
    public function setDefinitions(array $definitions)
    {
        $iconicDefininitions = array_map(array($this, 'toIconicDefinition'), $definitions);

        $this->_delegateContainerBuilder->setDefinitions($iconicDefininitions);
    }

    /**
     * Based heavily on ehough_iconic_compiler_MergeExtensionConfigurationPass.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $self
     *
     * @api
     */
    public function process(tubepress_platform_api_ioc_ContainerBuilderInterface $self)
    {
        $parameters = $this->_delegateContainerBuilder->getParameterBag()->all();

        /**
         * These will all be tubepress_platform_impl_ioc_Definition instances
         */
        $definitions = $self->getDefinitions();

        /**
         * @var $extension tubepress_platform_api_ioc_ContainerExtensionInterface
         */
        foreach ($this->_tubePressContainerExtensions as $extension) {

            $tmpContainer = new tubepress_platform_impl_ioc_ContainerBuilder($this->_delegateContainerBuilder->getParameterBag());

            $extension->load($tmpContainer);

            $this->merge($tmpContainer);
        }

        $self->addDefinitions($definitions);

        $this->_delegateContainerBuilder->getParameterBag()->add($parameters);
    }

    /**
     * Get all parameter names.
     *
     * @return string[] An array of all parameter names. May be empty.
     */
    public function getParameterNames()
    {
        return array_keys($this->_delegateContainerBuilder->getParameterBag()->all());
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
    private function merge(tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder)
    {
        $this->addDefinitions($containerBuilder->getDefinitions());
        $this->getParameterBag()->add($containerBuilder->getParameterBag()->all());
    }

    public function toTubePressDefinition(ehough_iconic_Definition $definition)
    {
        return tubepress_platform_impl_ioc_Definition::fromIconicDefinition($definition);
    }

    public function toIconicDefinition(tubepress_platform_api_ioc_DefinitionInterface $definition)
    {
        if ($definition instanceof tubepress_platform_impl_ioc_Definition) {

            return $definition->getUnderlyingIconicDefinition();
        }

        $cleanedArguments = $this->convertToIconicReferenceIfNecessary($definition->getArguments());
        $cleanedMethodCalls = $this->convertToIconicReferenceIfNecessary($definition->getMethodCalls());

        $toReturn = new ehough_iconic_Definition($definition->getClass(), $cleanedArguments);

        $toReturn->setConfigurator($definition->getConfigurator());
        $toReturn->setDecoratedService($definition->getDecoratedService());
        $toReturn->setFactoryClass($definition->getFactoryClass());
        $toReturn->setFactoryMethod($definition->getFactoryMethod());
        $toReturn->setFactoryService($definition->getFactoryService());
        $toReturn->setFile($definition->getFile());
        $toReturn->setMethodCalls($cleanedMethodCalls);
        $toReturn->setProperties($definition->getProperties());
        $toReturn->setTags($definition->getTags());

        return $toReturn;
    }

    public function convertToIconicReferenceIfNecessary($candidate)
    {
        if ($candidate instanceof tubepress_platform_api_ioc_Reference) {

            return new ehough_iconic_Reference("$candidate");
        }

        if (is_array($candidate)) {

            foreach ($candidate as $name => $value) {

                $candidate[$name] = $this->convertToIconicReferenceIfNecessary($candidate[$name]);
            }
        }

        return $candidate;
    }
}