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

/**
 * Discovers add-ons for TubePress.
 */
class tubepress_theme_ioc_compiler_ThemesPrimerPass implements tubepress_spi_ioc_CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_process($containerBuilder, 'themes');
        $this->_process($containerBuilder, 'admin-themes');
    }

    private function _process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, $id)
    {
        $logger        = $containerBuilder->get('tubepress_internal_logger_BootLogger');
        $finderFactory = $containerBuilder->get('finder_factory');
        $bootSettings  = $containerBuilder->get(tubepress_api_boot_BootSettingsInterface::_);
        $context       = $containerBuilder->get(tubepress_api_options_ContextInterface::_);
        $urlFactory    = $containerBuilder->get(tubepress_api_url_UrlFactoryInterface::_);
        $langUtils     = $containerBuilder->get(tubepress_api_util_LangUtilsInterface::_);
        $stringUtils   = $containerBuilder->get(tubepress_api_util_StringUtilsInterface::_);
        $serializer    = new tubepress_internal_boot_helper_uncached_Serializer($bootSettings);
        $factory       = new tubepress_internal_boot_helper_uncached_contrib_ThemeFactory(
            $context, $urlFactory, $langUtils, $logger, $stringUtils, $finderFactory
        );

        $manifestFinder = new tubepress_internal_boot_helper_uncached_contrib_ManifestFinder(
            TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . $id, DIRECTORY_SEPARATOR . $id, 'theme.json', $logger, $bootSettings, $finderFactory
        );

        $manifests = $manifestFinder->find();
        $themes    = array();
        foreach ($manifests as $path => $manifestData) {

            $theme = $factory->fromManifestData($path, $manifestData);

            if (is_array($theme)) {

                continue;
            }

            if ($theme->getName() === 'changeme/themename') {

                //ignore the starter theme
                continue;
            }

            $themes[] = $theme;
        }

        $bootArtifacts = $containerBuilder->getParameter(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS);
        $bootArtifacts = array_merge($bootArtifacts, array($id => $serializer->serialize($themes)));
        $containerBuilder->setParameter(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS, $bootArtifacts);
        $containerBuilder->set('tubepress_internal_boot_helper_uncached_contrib_ThemeFactory', $factory);
    }
}
