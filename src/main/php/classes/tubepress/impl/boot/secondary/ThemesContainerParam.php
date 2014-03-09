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
class tubepress_impl_boot_secondary_ThemesContainerParam implements tubepress_spi_boot_secondary_ThemesContainerParamInterface
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
     * @var tubepress_spi_theme_ThemeFinderInterface
     */
    private $_themeFinder;

    /**
     * @var ehough_finder_FinderFactoryInterface
     */
    private $_finderFactory;

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_spi_theme_ThemeFinderInterface $themeFinder, ehough_finder_FinderFactoryInterface $ffi)
    {
        $this->_themeFinder   = $themeFinder;
        $this->_finderFactory = $ffi;
        $this->_logger        = ehough_epilog_LoggerFactory::getLogger('Themes Container Param');
    }

    /**
     * Discovers TubePress themes.
     *
     * @return array An array data of the discovered TubePress themes.
     */
    public function getThemesContainerParameterValue()
    {
        $allThemes = $this->_themeFinder->findAllThemes();
        $toReturn  = array();

        /**
         * @var $theme tubepress_spi_theme_ThemeInterface
         */
        foreach ($allThemes as $theme) {

            $templates = $this->_findTemplates($theme->getRootFilesystemPath());

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

        return $toReturn;
    }

    private function _findTemplates($rootDirectory)
    {
        $this->_initLogging();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Looking for .tpl.php files in %s', $rootDirectory));
        }

        $finder   = $this->_finderFactory->createFinder()->files()->in($rootDirectory)->name('*.tpl.php');
        $toReturn = array();

        /**
         * @var $file SplFileInfo
         */
        foreach ($finder as $file) {

            $toReturn[] = ltrim(str_replace($rootDirectory, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d templates in %s', count($toReturn), $rootDirectory));
        }

        return $toReturn;
    }

    private function _initLogging()
    {
        if (!isset($this->_shouldLog)) {

            $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);
        }
    }
}
