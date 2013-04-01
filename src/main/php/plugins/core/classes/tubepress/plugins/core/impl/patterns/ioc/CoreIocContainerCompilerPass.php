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
 * Currently just builds the HTTP response handler.
 */
class tubepress_plugins_core_impl_patterns_ioc_CoreIocContainerCompilerPass implements ehough_iconic_api_compiler_ICompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container
     *
     * @return void
     */
    public final function process(ehough_iconic_impl_ContainerBuilder $container)
    {
        $this->_registerHttpResponseHandler($container);
    }

    private function _registerHttpResponseHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $chainId    = 'ehough_shortstop_impl_HttpResponseHandlerChain_chain';
        $commands   = $container->findTaggedServiceIds('tubepress.impl.http.ResponseHandler');
        $references = array();

        foreach ($commands as $id => $attributes) {

            $references[] = new ehough_iconic_impl_Reference($id);
        }

        $this->_registerChainDefinitionByReferences($container, $chainId, $references);

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            'ehough_shortstop_api_HttpResponseHandler',
            'ehough_shortstop_impl_HttpResponseHandlerChain'

        )->addArgument(new ehough_iconic_impl_Reference($chainId));
    }

    private function _registerChainDefinitionByReferences(ehough_iconic_impl_ContainerBuilder $container, $chainName, array $references)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->setDefinition(

            $chainName,
            new ehough_iconic_impl_Definition(

                'ehough_chaingang_api_Chain',
                $references
            )

        )->setFactoryClass('tubepress_plugins_core_impl_patterns_ioc_CoreIocContainerCompilerPass')
         ->setFactoryMethod('_buildChain');
    }

    public static function _buildChain()
    {
        $chain    = new ehough_chaingang_impl_StandardChain();
        $commands = func_get_args();

        foreach ($commands as $command) {

            $chain->addCommand($command);
        }

        return $chain;
    }
}