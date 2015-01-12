<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
class tubepress_platform_impl_boot_helper_uncached_contrib_ManifestFinder
{
    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var ehough_finder_FinderFactoryInterface
     */
    private $_finderFactory;

    /**
     * @var string
     */
    private $_systemStartingPoint;

    /**
     * @var string
     */
    private $_userStartingPoint;

    /**
     * @var string
     */
    private $_manifestName;

    public function __construct($systemStartingPoint,
                                $userStartingPoint,
                                $manifestName,
                                tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_platform_api_boot_BootSettingsInterface $bootSettings,
                                ehough_finder_FinderFactoryInterface              $finderFactory)
    {
        $this->_shouldLog           = $logger->isEnabled();
        $this->_logger              = $logger;
        $this->_bootSettings        = $bootSettings;
        $this->_finderFactory       = $finderFactory;
        $this->_systemStartingPoint = $systemStartingPoint;
        $this->_userStartingPoint   = $userStartingPoint;
        $this->_manifestName        = $manifestName;
    }

    /**
     * An associative array. The keys are the absolute paths to the manifests, and the values
     * are associative arrays containing the decoded manifest data.
     *
     * @return array
     */
    public function find()
    {
        $toReturn      = array();
        $manifestPaths = $this->_findManifestPaths();

        foreach ($manifestPaths as $manifestPath) {

            $decoded = null;

            try {

                $decoded = $this->_decodeManifestToAssociativeArray($manifestPath);

                $toReturn[$manifestPath] = $decoded;

            } catch (InvalidArgumentException $e) {

                if ($this->_shouldLog) {

                    $this->_logger->error($e->getMessage());
                    continue;
                }
            }
        }

        return $toReturn;
    }

    private function _decodeManifestToAssociativeArray($absPathToManifest)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Attempting to read and decode manifest at %s', $absPathToManifest));
        }

        $contentsRead = file_get_contents($absPathToManifest);

        if ($contentsRead === false) {

            throw new InvalidArgumentException(sprintf('Unable to read contents of manifest at %s', $absPathToManifest));
        }

        $decoded = json_decode($contentsRead, true);

        if ($decoded === null) {

            throw new InvalidArgumentException(sprintf('Unable to decode manifest at %s', $absPathToManifest));
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Succesfully read and decoded manifest at %s', $absPathToManifest));
        }

        return $decoded;
    }

    private function _findManifestPaths()
    {
        $systemManifests = $this->__findManifestPathsInDirectory($this->_systemStartingPoint);
        $userManifests   = $this->_findUserManifestPaths($this->_userStartingPoint);
        $allManifests    = array_merge($systemManifests, $userManifests);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d candidate manifests (%d system and %d user) on the filesystem',
                count($allManifests), count($systemManifests), count($userManifests)));
        }

        return $allManifests;
    }

    private function _findUserManifestPaths($userPath)
    {
        $userContentDir        = $this->_bootSettings->getUserContentDirectory();
        $userContributablesDir = $userContentDir . $userPath;
        $userContributables    = $this->__findManifestPathsInDirectory($userContributablesDir);

        return $userContributables;
    }

    public function __findManifestPathsInDirectory($directory)
    {
        if (! is_dir($directory)) {

            return array();
        }

        $finder   = $this->_finderFactory->createFinder();
        $toReturn = array();

        if ($this->_shouldLog) {

            $this->_logger->debug('Searching for manifests in ' . $directory);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $finder = $finder->followLinks()->files()->in($directory)->name($this->_manifestName)->depth('< 2');

        /**
         * @var $infoFile SplFileInfo
         */
        foreach ($finder as $infoFile) {

            $toReturn[] = $infoFile->getRealPath();
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d manifest(s) inside %s' , count($toReturn), $directory));
        }

        return $toReturn;
    }
}