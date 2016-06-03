<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_template_ioc_compiler_TemplatePathProvidersPass implements tubepress_spi_ioc_CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_doProcess($containerBuilder, '');
        $this->_doProcess($containerBuilder, '.admin');
    }

    private function _doProcess(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, $serviceSuffix)
    {
        if (!$containerBuilder->hasDefinition('Twig_Loader_Filesystem' . $serviceSuffix)) {

            return;
        }

        $twigFsLoaderDefinition = $containerBuilder->getDefinition('Twig_Loader_Filesystem' . $serviceSuffix);
        $providerIds            = $containerBuilder->findTaggedServiceIds('tubepress_spi_template_PathProviderInterface' . $serviceSuffix);

        foreach ($providerIds as $providerId => $tags) {

            /*
             * @var tubepress_spi_template_PathProviderInterface
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
