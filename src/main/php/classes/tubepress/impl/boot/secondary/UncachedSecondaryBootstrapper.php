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
 * Retrieves settings from a PHP file.
 */
class tubepress_impl_boot_secondary_UncachedSecondaryBootstrapper implements tubepress_spi_boot_secondary_SecondaryBootstrapperInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_spi_boot_secondary_ClassLoaderPrimerInterface
     */
    private $_bootHelperClassLoadingHelper;

    /**
     * @var tubepress_spi_boot_secondary_IocCompilerInterface
     */
    private $_bootHelperIocHelper;

    /**
     * @var tubepress_spi_addon_AddonFinderInterface
     */
    private $_bootHelperAddonDiscoverer;

    /**
     * @var tubepress_spi_boot_secondary_ThemesPrimerInterface
     */
    private $_bootHelperThemeDiscoverer;

    /**
     * @var tubepress_impl_ioc_IconicContainerBuilder
     */
    private $_containerBuilder;

    /**
     * @var ehough_iconic_dumper_DumperInterface
     */
    private $_containerDumper;

    public function __construct(
        $shouldLog,
        tubepress_spi_boot_secondary_ClassLoaderPrimerInterface $clsi,
        tubepress_spi_addon_AddonFinderInterface $adi,
        tubepress_spi_boot_secondary_ThemesPrimerInterface $tdi,
        tubepress_spi_boot_secondary_IocCompilerInterface $ici)
    {
        $this->_logger                       = ehough_epilog_LoggerFactory::getLogger('Uncached Secondary Bootstrapper');
        $this->_shouldLog                    = $shouldLog;
        $this->_bootHelperClassLoadingHelper = $clsi;
        $this->_bootHelperAddonDiscoverer    = $adi;
        $this->_bootHelperIocHelper          = $ici;
        $this->_bootHelperThemeDiscoverer    = $tdi;
    }

    public function getServiceContainer(
        tubepress_spi_boot_SettingsFileReaderInterface $sfri,
        ehough_pulsar_ComposerClassLoader $classLoader
    )
    {
        if ($sfri->isClassLoaderEnabled()) {

            $this->_bootHelperClassLoadingHelper->prime($classLoader);
        }

        $addons = $this->_bootHelperAddonDiscoverer->findAddons($sfri->getAddonBlacklistArray());

        if ($sfri->isClassLoaderEnabled()) {

            $this->_bootHelperClassLoadingHelper->addClassHintsForAddons($addons, $classLoader);
        }

        if (!isset($this->_containerBuilder)) {

            $this->_containerBuilder = new tubepress_impl_ioc_IconicContainerBuilder();
        }

        $this->_containerBuilder->setParameter('themes',   $this->_bootHelperThemeDiscoverer->getThemesContainerParameterValue());
        $this->_containerBuilder->setParameter('classMap', $classLoader->getClassMap());

        $this->_bootHelperIocHelper->compile($this->_containerBuilder, $addons);

        $container = $this->_saveNewBootToCache($this->_containerBuilder, $sfri);

        return $container;
    }

    /**
     * @param tubepress_impl_ioc_IconicContainerBuilder $containerBuilder
     * @param tubepress_spi_boot_SettingsFileReaderInterface $sfri
     *
     * @return ehough_iconic_ContainerInterface
     */
    private function _saveNewBootToCache(tubepress_impl_ioc_IconicContainerBuilder $containerBuilder,
        tubepress_spi_boot_SettingsFileReaderInterface $sfri)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Preparing to store boot to cache.');
        }

        $dumpedContainerText = $this->_getDumpedContainerAsString($containerBuilder->getDelegateIconicContainerBuilder());
        $storagePath         = $sfri->getCachedContainerStoragePath();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Now writing dumped container to %s', $storagePath));
        }

        $success = @file_put_contents($storagePath, $dumpedContainerText) !== false;

        if ($success) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Saved service container to %s. Now including it.', $storagePath));
            }

            if (!class_exists('TubePressServiceContainer', false)) {

                /** @noinspection PhpIncludeInspection */
                require $storagePath;
            }

        } else {

            if ($this->_shouldLog) {

                $this->_logger->warning(sprintf('Could not write service container to %s. Instead calling eval()', $storagePath));
            }

            $one                 = 1;
            $dumpedContainerText = str_replace('<php?', '', $dumpedContainerText, $one);

            eval($dumpedContainerText);
        }

        /** @noinspection PhpUndefinedClassInspection */
        return new TubePressServiceContainer();
    }

    private function _getDumpedContainerAsString(ehough_iconic_ContainerBuilder $containerBuilder)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Preparing to dump container builder to string');
        }

        $dumpConfig = array(

            'class' => 'TubePressServiceContainer'
        );

        if (!isset($this->_containerDumper)) {

            $this->_containerDumper = new ehough_iconic_dumper_PhpDumper($containerBuilder);
        }

        $dumped = $this->_containerDumper->dump($dumpConfig);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Done dumping container builder to string. Check the HTML source to view the full' .
                ' container. <div style="display:none"><pre>%s</pre></div>', $dumped));
        }

        return $dumped;
    }


    /**
     * This is here strictly for testing! :/
     *
     * @param tubepress_impl_ioc_IconicContainerBuilder $containerBuilder
     */
    public function __setContainerBuilder(tubepress_impl_ioc_IconicContainerBuilder $containerBuilder)
    {
        $this->_containerBuilder = $containerBuilder;
    }

    public function __setContainerDumper(ehough_iconic_dumper_DumperInterface $dumper)
    {
        $this->_containerDumper = $dumper;
    }
}
