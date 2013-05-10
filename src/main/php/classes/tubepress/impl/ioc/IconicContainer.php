<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
class tubepress_impl_ioc_IconicContainer extends ehough_iconic_ContainerBuilder implements tubepress_api_ioc_ContainerInterface, ehough_iconic_compiler_CompilerPassInterface
{
    public function __construct(ehough_iconic_parameterbag_ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);

        /**
         * Remove some advanced IOC container stuff that we don't use (yet). This makes TubePress boot about
         * 30% faster!
         */
        $compilerPassConfig = $this->getCompilerPassConfig();
        $compilerPassConfig->setOptimizationPasses(array());
        $compilerPassConfig->setRemovingPasses(array());
        $compilerPassConfig->setMergePass($this);

        /**
         * Turn off resource loading.
         */
        $this->setResourceTracking(false);
    }

    public function compile()
    {
        $this->getCompiler()->compile($this);

        $this->getParameterBag()->resolve();
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_ContainerExtensionInterface $extension
     */
    public function registerTubePressExtension(tubepress_api_ioc_ContainerExtensionInterface $extension)
    {
        $iconicExtension = new tubepress_impl_ioc_IconicContainerExtensionWrapper($extension);

        $this->registerExtension($iconicExtension);
        $this->loadFromExtension($iconicExtension->getAlias());
    }

    /**
     * @internal
     *
     * @param tubepress_api_ioc_CompilerPassInterface $pass
     *
     * @return tubepress_api_ioc_ContainerInterface The current instance.
     */
    public function addTubePressCompilerPass(tubepress_api_ioc_CompilerPassInterface $pass)
    {
        return $this->addCompilerPass(new tubepress_impl_ioc_IconicCompilerPassWrapper($pass));
    }

    /**
     * Gets a service definition.
     *
     * @param string $id The service identifier
     *
     * @return tubepress_api_ioc_Definition A tubepress_api_ioc_Definition instance, or null if the
     *                                      service does not exist.
     *
     * @api
     * @since 3.1.0
     */
    public function getDefinition($id)
    {
        try {

            return parent::getDefinition($id);

        } catch (ehough_iconic_exception_InvalidArgumentException $e) {

            return null;
        }
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
     * @return tubepress_api_ioc_Definition A tubepress_api_ioc_Definition instance
     *
     * @api
     * @since 3.1.0
     */
    public function register($id, $class = null)
    {
        return $this->setDefinition(strtolower($id), new tubepress_api_ioc_Definition($class));
    }

    /**
     * Sets a service definition.
     *
     * @param string                       $id         The service identifier
     * @param tubepress_api_ioc_Definition $definition A tubepress_api_ioc_Definition instance
     *
     * @return tubepress_api_ioc_Definition the service definition
     *
     * @throws BadMethodCallException When this ContainerBuilder is frozen
     *
     * @api
     * @since 3.1.0
     */
    public function addDefinition($id, tubepress_api_ioc_Definition $definition)
    {
        try {

            return $this->setDefinition($id, $definition);

        } catch (ehough_iconic_exception_BadMethodCallException $e) {

            throw new BadMethodCallException($e);
        }
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
    public function get($id, $invalidBehavior = ehough_iconic_ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (!$this->has($id)) {

            return null;
        }

        try {

            return parent::get($id);

        } catch (Exception $e) {

            throw new RuntimeException($e);
        }
    }

    public function process(ehough_iconic_ContainerBuilder $container)
    {
        $parameters = $container->getParameterBag()->all();
        $definitions = $container->getDefinitions();
        $aliases = $container->getAliases();

        foreach ($container->getExtensions() as $extension) {
            if ($extension instanceof ehough_iconic_extension_PrependExtensionInterface) {
                $extension->prepend($container);
            }
        }

        foreach ($container->getExtensions() as $name => $extension) {
            if (!$config = $container->getExtensionConfig($name)) {
                // this extension was not called
                continue;
            }
            $config = $container->getParameterBag()->resolveValue($config);

            $tmpContainer = new tubepress_impl_ioc_IconicContainer($container->getParameterBag());

            $extension->load($config, $tmpContainer);

            $container->merge($tmpContainer);
        }

        $container->addDefinitions($definitions);
        $container->addAliases($aliases);
        $container->getParameterBag()->add($parameters);
    }
}