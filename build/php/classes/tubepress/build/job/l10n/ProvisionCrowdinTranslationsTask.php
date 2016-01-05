<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_build_job_l10n_ProvisionCrowdinTranslationsTask extends tubepress_build_job_AbstractBuildTask
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $_filesystem;

    /**
     * @var string
     */
    private $_workingDirectory;

    /**
     * @var array
     */
    private $_localeMap;

    public function __construct(array $localeMap)
    {
        $this->_filesystem       = new \Symfony\Component\Filesystem\Filesystem();
        $this->_workingDirectory = $this->getStagingDirectory() . '/tubepress';
        $this->_localeMap        = $localeMap;
    }

    public function run()
    {
        $this->_downloadTranslations();
        $this->_compileTranslations();
        $this->_cleanupLeftovers();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Provision Crowdin translations';
    }

    private function _compileTranslations()
    {
        foreach ($this->_localeMap as $crowdinCode => $finalCode) {

            $this->_compileDirectory($crowdinCode, $finalCode);
        }
    }

    private function _compileDirectory($crowdinCode, $finalCode)
    {
        $finder     = new \Symfony\Component\Finder\Finder();
        $targetDir  = sprintf('%s/tubepress/src/translations/%s', $this->getStagingDirectory(), $crowdinCode);
        $poFiles    = $finder->files()->name('*.po')->in($targetDir);
        $poFiles    = iterator_to_array($poFiles);
        $poFiles    = implode(' ', $poFiles);
        $catCommand = sprintf('msgcat %s -o tubepress-%s.po', $poFiles, $finalCode);
        $compileCmd = sprintf('msgfmt tubepress-%s.po -o ../tubepress-%s.mo', $finalCode, $finalCode);

        $this->runProcess($catCommand, $targetDir);
        $this->runProcess($compileCmd, $targetDir);
    }

    private function _cleanupLeftovers()
    {
        $finder    = new \Symfony\Component\Finder\Finder();
        $targetDir = sprintf('%s/tubepress/src/translations', $this->getStagingDirectory());

        $finder->directories()->in($targetDir)->ignoreDotFiles(true);

        $this->_filesystem->remove($finder);
    }

    private function _downloadTranslations()
    {
        $crowdinCliJarPath = $this->getBuildDirectory() . '/bin/crowdin-cli.jar';
        $crowdinConfigFile = $this->getProjectDirectory() . '/src/translations/crowdin-cli-config.yaml';

        $this->runProcess(
            sprintf('java -jar %s -c %s download', $crowdinCliJarPath, $crowdinConfigFile),
            $this->_workingDirectory
        );
    }
}