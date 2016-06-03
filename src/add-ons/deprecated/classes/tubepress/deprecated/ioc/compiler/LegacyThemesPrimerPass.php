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

class tubepress_deprecated_ioc_compiler_LegacyThemesPrimerPass implements tubepress_spi_ioc_CompilerPassInterface
{
    private static $_LEGACY_TEMPLATE_MAP = array(
        'gallery/main.tpl.php'  => 'gallery.tpl.php',
        'search/input.tpl.php'  => 'search/search_input.tpl.php',
        'search/output.tpl.php' => 'search/search_output.tpl.php',
        'single/main.tpl.php'   => 'single_video.tpl.php',
    );

    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     *
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!$containerBuilder->hasParameter(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS)) {

            return;
        }

        $bootArtifacts = $containerBuilder->getParameter(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS);

        if (!is_array($bootArtifacts) || !isset($bootArtifacts['themes'])) {

            return;
        }

        $serializedThemes        = $bootArtifacts['themes'];
        $bootSettings            = $containerBuilder->get(tubepress_api_boot_BootSettingsInterface::_);
        $serializer              = new tubepress_internal_boot_helper_uncached_Serializer($bootSettings);
        $unserializedThemes      = $serializer->unserialize($serializedThemes);
        $adjustedSystemThemes    = $this->_adjustLegacySystemThemes($containerBuilder, $unserializedThemes);
        $userLegacyThemes        = $this->_findUserLegacyThemes($bootSettings, $containerBuilder);
        $allLegacyThemes         = array_merge($adjustedSystemThemes, $userLegacyThemes);
        $allThemes               = array_merge($unserializedThemes, $allLegacyThemes);
        $serializedThemes        = $serializer->serialize($allThemes);
        $bootArtifacts['themes'] = $serializedThemes;

        $containerBuilder->setParameter(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS, $bootArtifacts);
    }

    private function _adjustLegacySystemThemes(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, array $allThemes)
    {
        $toReturn = array();

        /**
         * @var tubepress_api_util_StringUtilsInterface
         */
        $stringUtils = $containerBuilder->get(tubepress_api_util_StringUtilsInterface::_);

        foreach ($allThemes as $systemTheme) {

            if (!($systemTheme instanceof tubepress_internal_theme_FilesystemTheme)) {

                $toReturn[] = $systemTheme;
                continue;
            }

            $themeName = $systemTheme->getName();

            if (!$stringUtils->startsWith($themeName, 'tubepress/legacy-')) {

                $toReturn[] = $systemTheme;
                continue;
            }

            $themePath   = $systemTheme->getThemePath();
            $templateMap = $this->_getTemplateMapForLegacyDirectory($containerBuilder, $themePath);

            if ($templateMap) {

                $systemTheme->setTemplateNamesToAbsPathsMap($templateMap);
            }

            $toReturn[] = $systemTheme;
        }

        return $toReturn;
    }

    private function _getTemplateMapForLegacyDirectory(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder, $path)
    {
        /**
         * @var tubepress_internal_finder_FinderFactory
         */
        $finderFactory = $containerBuilder->get('finder_factory');

        /**
         * @var tubepress_api_util_StringUtilsInterface
         */
        $stringUtils = $containerBuilder->get(tubepress_api_util_StringUtilsInterface::_);
        $finder      = $finderFactory->createFinder();
        $templates   = $finder->files()->in($path)->ignoreDotFiles(true)->ignoreVCS(true)->name('*.tpl.php');
        $toReturn    = array();

        if (count($templates) === 0) {

            return $toReturn;
        }

        foreach ($templates as $splFileInfo) {

            $templatePath = "$splFileInfo";

            foreach (self::$_LEGACY_TEMPLATE_MAP as $modernTemplateName => $legacySuffix) {

                if ($stringUtils->endsWith($templatePath, $legacySuffix)) {

                    $toReturn[$modernTemplateName] = $templatePath;
                    break;
                }
            }

            if (preg_match_all('/^.*\/players\/([a-zA-Z]+)\.tpl\.php$/', $templatePath, $matches) === 1) {

                $playerNames = $matches[1];

                if (is_array($playerNames) && count($playerNames) === 1) {

                    $playerName                                             = $playerNames[0];
                    $toReturn["gallery/players/$playerName/ajax.tpl.php"]   = $templatePath;
                    $toReturn["gallery/players/$playerName/static.tpl.php"] = $templatePath;
                }
            }
        }

        return $toReturn;
    }

    private function _findUserLegacyThemes(tubepress_api_boot_BootSettingsInterface    $bootSettings,
                                           tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $userThemeDir = $bootSettings->getUserContentDirectory() . '/themes';

        if (!is_dir($userThemeDir)) {

            return array();
        }

        /**
         * @var tubepress_internal_finder_FinderFactory
         */
        $finderFactory = $containerBuilder->get('finder_factory');
        $finder        = $finderFactory->createFinder()->directories()->in($userThemeDir)->depth('< 1');
        $toReturn      = array();

        /**
         * @var SplFileInfo
         */
        foreach ($finder as $candidateLegacyThemeDir) {

            $themeRoot = "$candidateLegacyThemeDir";
            $baseName  = basename($themeRoot);

            if ($baseName === 'starter') {

                /*
                 * Ignore the starter theme.
                 */
                continue;
            }

            if (is_file("$themeRoot/theme.json")) {

                continue;
            }

            /**
             * @var tubepress_internal_boot_helper_uncached_contrib_ThemeFactory
             */
            $themeFactory = $containerBuilder->get('tubepress_internal_boot_helper_uncached_contrib_ThemeFactory');
            $templateMap  = $this->_getTemplateMapForLegacyDirectory($containerBuilder, $themeRoot);
            $manifestPath = $bootSettings->getPathToSystemCacheDirectory() . DIRECTORY_SEPARATOR . 'foobar';
            $manifestData = array(
                'name'    => "unknown/legacy-$baseName",
                'version' => '1.0.0',
                'title'   => "$baseName (legacy)",
                'authors' => array(
                    array('name' => 'Unknown'),
                ),
                'license' => array(
                    'type' => 'MPL-2.0',
                    'urls' => array('http://www.mozilla.org/MPL/2.0/'),
                ),
                'description' => "TubePress 3.x.x theme auto-generated from $themeRoot",
            );
            $theme = $themeFactory->fromManifestData($manifestPath, $manifestData);

            if (!($theme instanceof tubepress_internal_theme_FilesystemTheme)) {

                continue;
            }

            $theme->setParentThemeName('tubepress/legacy-default');
            $theme->setTemplateNamesToAbsPathsMap($templateMap);
            $theme->setManifestPath($manifestPath);

            $toReturn[] = $theme;
        }

        return $toReturn;
    }
}
