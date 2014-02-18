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
 * Builds instances of ehough_chaingang_api_Chain
 */
class tubepress_impl_ioc_ChainRegistrar
{
    public static function registerChainDefinitionByReferences(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, $chainName, array $references)
    {
        $containerBuilder->setDefinition(

            $chainName,
            new tubepress_impl_ioc_Definition(

                'ehough_chaingang_api_Chain',
                $references
            )

        )->setFactoryClass('tubepress_impl_ioc_ChainRegistrar')
         ->setFactoryMethod('buildChain');
    }

    public static function registerChainDefinitionByClassNames(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder,
                                                               $chainName, array $classNames)
    {
        $references = array();

        foreach ($classNames as $className) {

            $containerBuilder->register($className, $className);

            array_push($references, new ehough_iconic_Reference($className));
        }

        self::registerChainDefinitionByReferences($containerBuilder, $chainName, $references);
    }

    public static function buildChain()
    {
        $chain    = new ehough_chaingang_impl_StandardChain();
        $commands = func_get_args();

        foreach ($commands as $command) {

            $chain->addCommand($command);
        }

        return $chain;
    }
}