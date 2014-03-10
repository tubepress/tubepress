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
class tubepress_impl_theme_ThemeFinder extends tubepress_impl_contrib_AbstractContributableFinder implements tubepress_spi_theme_ThemeFinderInterface
{
    private static $_LEGACY_THEME_NAME_PREFIX = 'unknown/legacy-';

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * Discovers TubePress themes.
     *
     * @return array An array data of the discovered TubePress themes.
     */
    public function findAllThemes()
    {
        $modernThemes = $this->findContributables('/src/main/resources/default-themes/', '/themes');
        $legacyThemes = $this->_findLegacyThemes($modernThemes);
        $toReturn     = array_merge($modernThemes, $legacyThemes);

        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('Found %d theme(s) (%d modern and %d legacy)',

                count($toReturn), count($modernThemes), count($legacyThemes)
            ));
        }

        return $toReturn;
    }

    /**
     * @return array A map of optional attributes.
     */
    protected function getOptionalAttributesMap()
    {
        return array(

            tubepress_impl_theme_ThemeBase::ATTRIBUTE_PARENT   => 'ParentThemeName',
            tubepress_impl_theme_ThemeBase::CATEGORY_RESOURCES => array(

                tubepress_impl_theme_ThemeBase::ATTRIBUTE_SCRIPTS => 'Scripts',
                tubepress_impl_theme_ThemeBase::ATTRIBUTE_STYLES  => 'Styles',
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

    protected function getManifestName()
    {
        return 'theme.json';
    }

    protected function getLogger()
    {
        if (!isset($this->_logger)) {

            $this->_logger = ehough_epilog_LoggerFactory::getLogger('Theme Finder');
        }

        return $this->_logger;
    }

    protected function getAdditionalRequiredConstructorArgs(array $manifestContents, $absPath)
    {
        $pathElements = array(
            TUBEPRESS_ROOT,
            'src', 'main', 'resources', 'default-themes'
        );

        $needle   = implode(DIRECTORY_SEPARATOR, $pathElements);
        $isSystem = strpos($absPath, $needle) !== false;

        return array($isSystem, dirname($absPath));
    }

    private function _findLegacyThemes(array $modernThemes)
    {
        $userThemeDir = $this->getEnvironmentDetector()->getUserContentDirectory() . '/themes';

        if (!is_dir($userThemeDir)) {

            if ($this->shouldLog()) {

                $this->_logger->debug(sprintf('User theme directory at %s does not exist.',
                    $userThemeDir
                ));
            }

            return array();
        }

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

                if (strpos($candidateLegacyThemeDir->getRealPath(), $modernTheme->getRootFilesystemPath()) !== false) {

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

                self::$_LEGACY_THEME_NAME_PREFIX . basename($legacyThemeDirectory),
                '1.0.0',
                ucwords(basename($legacyThemeDirectory)) . ' (legacy)',
                array('name' => 'unknown'),
                array(array('type' => 'MIT', 'url' => 'http://opensource.org/licenses/MIT')),
                false,
                $legacyThemeDirectory
            );

            $theme->setParentThemeName('tubepress/legacy-default');

            $toReturn[] = $theme;
        }

        return $toReturn;
    }


}
