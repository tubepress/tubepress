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
class tubepress_impl_boot_secondary_ThemeDiscoverer extends tubepress_impl_boot_secondary_AbstractContributableDiscoverer implements tubepress_spi_boot_secondary_ThemeDiscoveryInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * Discovers TubePress themes.
     *
     * @return array An array data of the discovered TubePress themes.
     */
    public function getThemesContainerParameterValue()
    {
        $modernThemes = $this->findContributables('/src/main/resources/default-themes/', '/themes');
        $legacyThemes = $this->_findLegacyThemes($modernThemes);
        $allThemes    = array_merge($modernThemes, $legacyThemes);
        $toReturn     = array();

        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('Found %d theme(s) (%d modern and %d legacy)',

                count($allThemes), count($modernThemes), count($legacyThemes)
            ));
        }

        /**
         * @var $theme tubepress_spi_theme_ThemeInterface
         */
        foreach ($allThemes as $theme) {

            $manifest  = $theme->getAbsolutePathToManifest();
            $scripts   = $theme->getScripts();
            $styles    = $theme->getStyles();
            $title     = $theme->getTitle();
            $parent    = $theme->getParentThemeName();
            $templates = $this->_findThemeTemplates(dirname($manifest));
            $isSys     = $this->_isSystemTheme(dirname($manifest));

            $toReturn[$theme->getName()] = array(

                'title'         => $title,
                'manifestPath'  => $manifest,
                'styles'        => $styles,
                'scripts'       => $scripts,
                'parent'        => $parent,
                'templates'     => $templates,
                'isSystemTheme' => $isSys,
            );
        }

        return $toReturn;
    }

    /**
     * @return array A map of required attributes.
     */
    protected function getRequiredAttributesMap()
    {
        return array();
    }

    /**
     * @return array A map of optional attributes.
     */
    protected function getOptionalAttributesMap()
    {
        return array(

            'parent'    => 'ParentThemeName',
            'resources' => array(

                'scripts' => 'Scripts',
                'styles'  => 'Styles',
            ),
        );
    }

    /**
     * @return string The class name that this discoverer instantiates.
     */
    protected function getContributableClassName()
    {
        return 'tubepress_impl_theme_ThemeBase';
    }

    protected function getAdditionalRequiredConstructorArgs(array $manifestContents, $absPath)
    {
        return array(realpath($absPath));
    }

    protected function getManifestName()
    {
        return 'theme.json';
    }

    protected function getLogger()
    {
        if (!isset($this->_logger)) {

            $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Theme Discoverer');
        }

        return $this->_logger;
    }

    private function _findLegacyThemes(array $modernThemes)
    {
        $userThemeDir = $this->getEnvironmentDetector()->getUserContentDirectory() . '/themes';
        $finder       = $this->getFinderFactory()->createFinder()->directories()->in($userThemeDir)->depth('< 1');
        $themesToKeep = array();

        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('Looking for legacy themes in %s. %d candidates.',
                $userThemeDir, count($finder)
            ));
        }

        /**
         * @var $candidateLegacyThemeDir SplFileInfo
         */
        foreach ($finder as $candidateLegacyThemeDir) {

            $keepTheme = true;

            /**
             * @var $modernTheme tubepress_spi_theme_ThemeInterface
             */
            foreach ($modernThemes as $modernTheme) {

                if (strpos($candidateLegacyThemeDir->getRealPath(), dirname($modernTheme->getAbsolutePathToManifest())) !== false) {

                    $keepTheme = false;
                    break;
                }
            }

            if ($keepTheme) {

                if ($this->shouldLog()) {

                    $this->_logger->debug(sprintf('Considering %s as a legacy theme.', $candidateLegacyThemeDir->getRealPath()));
                }

                $themesToKeep[] = $candidateLegacyThemeDir->getRealPath();

            } else {

                if ($this->shouldLog()) {

                    $this->_logger->debug(sprintf('Determined that %s is not a legacy theme.', $candidateLegacyThemeDir));
                }
            }
        }

        $toReturn = array();

        foreach ($themesToKeep as $legacyThemeDirectory) {

            $theme = new tubepress_impl_theme_ThemeBase(

                'unknown/legacy-' . basename($legacyThemeDirectory),
                '1.0.0',
                ucwords(basename($legacyThemeDirectory)) . ' (legacy)',
                array('name' => 'unknown'),
                array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')),
                "$legacyThemeDirectory/theme.json"
            );

            $toReturn[] = $theme;
        }

        return $toReturn;
    }

    private function _findThemeTemplates($rootDirectory)
    {
        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('Looking for .tpl.php files in %s', $rootDirectory));
        }

        $finder   = $this->getFinderFactory()->createFinder()->files()->in($rootDirectory)->name('*.tpl.php');
        $toReturn = array();

        /**
         * @var $file SplFileInfo
         */
        foreach ($finder as $file) {

            $toReturn[] = ltrim(str_replace($rootDirectory, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        }

        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('Found %d templates in %s', count($toReturn), $rootDirectory));
        }

        return $toReturn;
    }

    private function _isSystemTheme($absolutePath)
    {
        $pathElements = array(
            TUBEPRESS_ROOT,
            'src', 'main', 'resources', 'default-themes'
        );
        $needle = implode(DIRECTORY_SEPARATOR, $pathElements);

        return strpos($absolutePath, $needle) !== false;
    }
}
