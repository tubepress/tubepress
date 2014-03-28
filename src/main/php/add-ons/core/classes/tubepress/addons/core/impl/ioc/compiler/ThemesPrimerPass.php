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
class tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass implements tubepress_api_ioc_CompilerPassInterface
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
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Themes Container Param');
    }

    /**
     * Provides add-ons with the ability to modify the TubePress IOC container builder
     * before it is compiled for production.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The core IOC container builder.
     *
     * @return void
     * @api
     * @since 3.1.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $themeFinder   = $containerBuilder->get(tubepress_spi_theme_ThemeFinderInterface::_);
        $finderFactory = $containerBuilder->get('ehough_finder_FinderFactoryInterface');
        $allThemes     = $themeFinder->findAllThemes();
        $toReturn      = array();

        /**
         * @var $theme tubepress_spi_theme_ThemeInterface
         */
        foreach ($allThemes as $theme) {

            $templates = $this->_findTemplates($theme->getRootFilesystemPath(), $finderFactory);

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

    private function _findTemplates($rootDirectory, ehough_finder_FinderFactoryInterface $ffi)
    {
        $this->_initLogging();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Looking for .tpl.php files in %s', $rootDirectory));
        }

        $finder   = $ffi->createFinder()->files()->in($rootDirectory)->name('*.tpl.php');
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
