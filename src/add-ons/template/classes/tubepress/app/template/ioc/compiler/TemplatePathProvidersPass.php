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

class tubepress_app_template_ioc_compiler_TemplatePathProvidersPass implements tubepress_platform_api_ioc_CompilerPassInterface
{
    /**
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_doProcess($containerBuilder, '');
        $this->_doProcess($containerBuilder, '.admin');
    }

    private function _doProcess(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder, $serviceSuffix)
    {
        if (!$containerBuilder->hasDefinition('Twig_Loader_Filesystem' . $serviceSuffix)) {

            return;
        }

        $twigFsLoaderDefinition = $containerBuilder->getDefinition('Twig_Loader_Filesystem' . $serviceSuffix);
        $providerIds            = $containerBuilder->findTaggedServiceIds('tubepress_lib_api_template_PathProviderInterface' . $serviceSuffix);

        foreach ($providerIds as $providerId => $tags) {

            /**
             * @var $provider tubepress_lib_api_template_PathProviderInterface
             */
            $provider    = $containerBuilder->get($providerId);
            $directories = $provider->getTemplateDirectories();

            foreach ($directories as $directory) {

                if (is_dir($directory)) {

                    $twigFsLoaderDefinition->addMethodCall('addPath', array($directory));
                }
            }
        }
    }
}