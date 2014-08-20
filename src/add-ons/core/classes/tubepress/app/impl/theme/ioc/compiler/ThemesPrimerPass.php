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
 * Discovers add-ons for TubePress.
 */
class tubepress_app_impl_theme_ioc_compiler_ThemesPrimerPass implements tubepress_platform_api_ioc_CompilerPassInterface
{
    private static $_LEGACY_THEME_NAME_PREFIX = 'unknown/legacy-';

    /**
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $logger         = $containerBuilder->get('tubepress_platform_impl_log_BootLogger');
        $finderFactory  = $containerBuilder->get('ehough_finder_FinderFactoryInterface');
        $bootSettings   = $containerBuilder->get(tubepress_platform_api_boot_BootSettingsInterface::_);
        $context        = $containerBuilder->get(tubepress_app_api_options_ContextInterface::_);
        $urlFactory     = $containerBuilder->get(tubepress_platform_api_url_UrlFactoryInterface::_);
        $langUtils      = $containerBuilder->get(tubepress_platform_api_util_LangUtilsInterface::_);
        $stringUtils    = $containerBuilder->get(tubepress_platform_api_util_StringUtilsInterface::_);
        $serializer     = new tubepress_platform_impl_boot_helper_uncached_Serializer($bootSettings);

        $manifestFinder = new tubepress_platform_impl_boot_helper_uncached_contrib_ManifestFinder(
            TUBEPRESS_ROOT . '/web/themes', '/themes', 'theme.json', $logger, $bootSettings, $finderFactory
        );
        $factory = new tubepress_platform_impl_boot_helper_uncached_contrib_ThemeFactory(
            $context, $urlFactory, $langUtils, $logger, $stringUtils, $finderFactory
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

        $bootArtifacts = $containerBuilder->getParameter(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS);
        $bootArtifacts = array_merge($bootArtifacts, array('themes' => $serializer->serialize($themes)));
        $containerBuilder->setParameter(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS, $bootArtifacts);
        $containerBuilder->set('tubepress_platform_impl_boot_helper_uncached_contrib_ThemeFactory', $factory);
    }
}
