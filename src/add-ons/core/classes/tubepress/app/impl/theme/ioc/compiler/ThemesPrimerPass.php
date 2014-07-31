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

            $themes[] = $theme;
        }

        $bootArtifacts = $containerBuilder->getParameter(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS);
        $bootArtifacts = array_merge($bootArtifacts, array('themes' => $serializer->serialize($themes)));
        $containerBuilder->setParameter(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS, $bootArtifacts);
    }



//    private function _findLegacyThemes(array $modernThemes)
//    {
//        $userThemeDir = $this->getBootSettings()->getUserContentDirectory() . '/themes';
//
//        if (!is_dir($userThemeDir)) {
//
//            if ($this->shouldLog()) {
//
//                $this->getLogger()->debug(sprintf('User theme directory at %s does not exist.',
//                    $userThemeDir
//                ));
//            }
//
//            return array();
//        }
//
//        $finder       = $this->getFinderFactory()->createFinder()->directories()->in($userThemeDir)->depth('< 1');
//        $themesToKeep = array();
//
//        if ($this->shouldLog()) {
//
//            $this->getLogger()->debug(sprintf('Looking for legacy themes in %s. %d candidates.',
//                $userThemeDir, count($finder)
//            ));
//        }
//
//        /**
//         * @var $candidateLegacyThemeDir SplFileInfo
//         */
//        foreach ($finder as $candidateLegacyThemeDir) {
//
//            $keepTheme = true;
//
//            if (basename($candidateLegacyThemeDir->getPathname()) === 'starter') {
//
//                /**
//                 * Ignore the starter theme.
//                 */
//                continue;
//            }
//
//            /**
//             * @var $modernTheme tubepress_app_api_theme_ThemeInterface
//             */
//            foreach ($modernThemes as $modernTheme) {
//
//                if (strpos($candidateLegacyThemeDir->getPathname(), $modernTheme->getRootFilesystemPath()) !== false) {
//
//                    $keepTheme = false;
//                    break;
//                }
//            }
//
//            if ($keepTheme) {
//
//                if ($this->shouldLog()) {
//
//                    $this->getLogger()->debug(sprintf('Considering %s as a legacy theme.', $candidateLegacyThemeDir->getPathname()));
//                }
//
//                $themesToKeep[] = $candidateLegacyThemeDir->getPathname();
//
//            } else {
//
//                if ($this->shouldLog()) {
//
//                    $this->getLogger()->debug(sprintf('Determined that %s is not a legacy theme.', $candidateLegacyThemeDir));
//                }
//            }
//        }
//
//        $toReturn = array();
//
//        foreach ($themesToKeep as $legacyThemeDirectory) {
//
////            $theme = new tubepress_app_impl_theme_AbstractTheme(
////
////                self::$_LEGACY_THEME_NAME_PREFIX . basename($legacyThemeDirectory),
////                '1.0.0',
////                ucwords(basename($legacyThemeDirectory)) . ' (legacy)',
////                array('name' => 'unknown'),
////                array(array('type' => 'MIT', 'url' => 'http://opensource.org/licenses/MIT')),
////                false,
////                $legacyThemeDirectory
////            );
////
////            $theme->setParentThemeName('tubepress/legacy-default');
////
////            $toReturn[] = $theme;
//        }
//
//        return $toReturn;
//    }
//
//    protected function filter(array &$contributables)
//    {
//        $contributables = array_filter($contributables, array($this, '__callbackRemoveStarterTheme'));
//        $contributables = array_filter($contributables, array($this, '__removeInvalidThemes'));
//    }
//
//
//    public function __callbackRemoveStarterTheme($element)
//    {
//        if (!($element instanceof tubepress_app_api_theme_ThemeInterface)) {
//
//            return true;
//        }
//
//        return $element->getName() !== 'changeme/themename';
//    }


}
