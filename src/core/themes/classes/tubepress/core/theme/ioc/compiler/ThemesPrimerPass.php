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
class tubepress_core_theme_ioc_compiler_ThemesPrimerPass implements tubepress_api_ioc_CompilerPassInterface
{
    const ATTRIBUTE_IS_SYSTEM   = 'isSystem';
    const ATTRIBUTE_PARENT      = 'parent';
    const ATTRIBUTE_SCREENSHOTS = 'screenshots';
    const ATTRIBUTE_SCRIPTS     = 'scripts';
    const ATTRIBUTE_STYLES      = 'styles';
    const ATTRIBUTE_TEMPLATES   = 'templates';
    const ATTRIBUTE_THEME_ROOT  = 'themeRoot';
    const ATTRIBUTE_TITLE       = 'title';

    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $logger        = $containerBuilder->get('tubepress_impl_log_BootLogger');
        $finderFactory = $containerBuilder->get('ehough_finder_FinderFactoryInterface');
        $registries    = $containerBuilder->findTaggedServiceIds(tubepress_api_contrib_RegistryInterface::_);
        $themeRegistry = null;

        foreach ($registries as $id => $tagArray) {

            foreach ($tagArray as $tag) {

                if (!isset($tag['type'])) {

                    continue;
                }

                if ($tag['type'] === tubepress_core_theme_api_ThemeInterface::_) {

                    $themeRegistry = $containerBuilder->get($id);
                    break;
                }
            }
        }

        if ($themeRegistry === null) {

            return;
        }

        $allThemes     = $themeRegistry->getAll();
        $toReturn      = array();

        /**
         * @var $theme tubepress_core_theme_api_ThemeInterface
         */
        foreach ($allThemes as $theme) {

            $templates = $this->_findTemplates($theme->getRootFilesystemPath(), $finderFactory, $logger);

            $toReturn[$theme->getName()] = array(

                self::ATTRIBUTE_IS_SYSTEM   => $theme->isSystemTheme(),
                self::ATTRIBUTE_PARENT      => $theme->getParentThemeName(),
                self::ATTRIBUTE_SCREENSHOTS => $theme->getScreenshots(),
                self::ATTRIBUTE_SCRIPTS     => $theme->getScripts(),
                self::ATTRIBUTE_STYLES      => $theme->getStyles(),
                self::ATTRIBUTE_TEMPLATES   => $templates,
                self::ATTRIBUTE_THEME_ROOT  => $theme->getRootFilesystemPath(),
                self::ATTRIBUTE_TITLE       => $theme->getTitle(),
            );
        }

        $containerBuilder->setParameter('themes', $toReturn);
    }

    private function _findTemplates($rootDirectory, ehough_finder_FinderFactoryInterface $ffi,
        tubepress_api_log_LoggerInterface $logger)
    {
        if ($logger->isEnabled()) {

            $logger->debug(sprintf('Looking for .tpl.php files in %s', $rootDirectory));
        }

        $finder   = $ffi->createFinder()->files()->in($rootDirectory)->name('*.tpl.php');
        $toReturn = array();

        /**
         * @var $file SplFileInfo
         */
        foreach ($finder as $file) {

            $toReturn[] = ltrim(str_replace($rootDirectory, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        }

        if ($logger->isEnabled()) {

            $logger->debug(sprintf('Found %d templates in %s', count($toReturn), $rootDirectory));
        }

        return $toReturn;
    }
}
