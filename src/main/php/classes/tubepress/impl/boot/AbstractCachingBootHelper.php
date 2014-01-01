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
 * Performs generic caching tasks for boot helpers.
 */
abstract class tubepress_impl_boot_AbstractCachingBootHelper
{
    /**
     * @return string
     */
    protected abstract function getBootCacheConfigElementName();

    /**
     * @return ehough_epilog_Logger
     */
    protected abstract function getLogger();

    /**
     * @param string $string The contents of the cache file, or the cache file path.
     *
     * @return object The hydrated object, or null if there was a problem.
     */
    protected abstract function hydrate($string);

    /**
     * @param object $object The object to convert to a string for the cache.
     *
     * @return string The string representation of the object, or null if there was a problem.
     */
    protected abstract function toString($object);

    /**
     * @return bool True if we should log, false otherwise.
     */
    protected abstract function shouldLog();

    /**
     * @return null|object The cached copy of the object, or null if unable to retrieve from cache.
     */
    protected function getCachedObject($hydrateFromCacheFileContents = true)
    {
        $bootConfigService = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperConfigService();
        $elementName       = $this->getBootCacheConfigElementName();
        $isCachingEnabled  = $bootConfigService->isCacheEnabledForElement($elementName);
        $logger            = $this->getLogger();
        $shouldLog         = $this->shouldLog();

        $this->_killCacheIfNeeded($logger, $shouldLog);

        if (!$isCachingEnabled) {

            if ($shouldLog) {

                $logger->debug(sprintf('Caching for %s is disabled', $elementName));
            }

            return null;
        }

        $filePath = $bootConfigService->getAbsolutePathToCacheFileForElement($elementName);

        if ($hydrateFromCacheFileContents) {

            if (!is_file($filePath) || !is_readable($filePath)) {

                if ($shouldLog) {

                    $logger->debug(sprintf('Cache file (%s) is not a readable file', $filePath));
                }

                return null;
            }

            if ($shouldLog) {

                $logger->debug(sprintf('Attempting read contents of cache file at %s', $filePath));
            }

            $contents = file_get_contents($filePath);

            if ($contents === false) {

                if ($shouldLog) {

                    $logger->debug(sprintf('Could not read cache file at %s', $filePath));
                }

                return null;
            }

        } else {

            $contents = $filePath;
        }

        if ($shouldLog) {

            $logger->debug(sprintf('Attempting to hydrate from cache file at %s', $filePath));
        }

        $hydrated = $this->hydrate($contents);

        if ($hydrated === false) {

            if ($shouldLog) {

                $logger->warn(sprintf('Could not hydrate data from cache file at %s', $filePath));
            }

            return null;
        }

        if ($shouldLog) {

            $logger->debug(sprintf('Successfully hydrated from cache file at %s', $filePath));
        }

        return $hydrated;
    }

    /**
     * @param mixed $data The object to cache.
     *
     * @return bool True if the file was cached successfully, false otherwise.
     */
    protected function tryToCache($data)
    {
        $bootConfigService = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperConfigService();
        $elementName       = $this->getBootCacheConfigElementName();
        $isCachingEnabled  = $bootConfigService->isCacheEnabledForElement($elementName);
        $logger            = $this->getLogger();
        $shouldLog         = $this->shouldLog();

        if (!$isCachingEnabled) {

            //we've already logged this
            return false;
        }

        $asString = $this->toString($data);

        if ($data === null) {

            if ($shouldLog) {

                $logger->warn(sprintf('Could not convert %s to string for cache', $elementName));
            }

            return false;
        }

        $filePath        = $bootConfigService->getAbsolutePathToCacheFileForElement($elementName);
        $parentDirectory = dirname($filePath);

        if (!is_dir($parentDirectory)) {

            if ($shouldLog) {

                $logger->debug(sprintf('Cache directory %s does not exist. Trying to create it now...', $parentDirectory));
            }

            $result = @mkdir($parentDirectory, 0755, true);

            if ($result === false) {

                if ($shouldLog) {

                    $logger->warn(sprintf('Could not create directory %s', $parentDirectory));
                }

                return false;
            }
        }

        if ($shouldLog) {

            $logger->debug(sprintf('Now attempting to cache %s to file at %s', $elementName, $filePath));
        }

        $written = @file_put_contents($filePath, $asString);

        if ($written === false) {

            if ($shouldLog) {

                $logger->warn(sprintf('Could not cache %s since %s is not a writable file', $elementName, $filePath));
            }

            return false;
        }

        if ($shouldLog) {

            $logger->debug(sprintf('Successfully cached %s to %s', $elementName, $filePath));
        }

        return true;
    }

    /**
     * @param string $string The contents of the cache file.
     *
     * @return object The hydrated object, or null if there was a problem.
     */
    protected function hydrateByDeserialization($string)
    {
        $result = @unserialize($string);

        return $result === false ? null : $result;
    }

    /**
     * @param object $object The object to convert to a string for the cache.
     *
     * @return string The string representation of the object, or null if there was a problem.
     */
    protected function toStringBySerialization($object)
    {
        $result = @serialize($object);

        return $result === false ? null : $result;
    }

    private function _killCacheIfNeeded(ehough_epilog_Logger $logger, $shouldLog)
    {
        $bootConfigService = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperConfigService();
        $shouldKillCache   = $bootConfigService->isCacheKillerTurnedOn();

        if (!$shouldKillCache) {

            return;
        }

        $elementName = $this->getBootCacheConfigElementName();
        $filePath    = $bootConfigService->getAbsolutePathToCacheFileForElement($elementName);

        if (!file_exists($filePath)) {

            return;
        }

        if ($shouldLog) {

            $logger->debug(sprintf('Attempting to delete cache file for %s at %s', $elementName, $filePath));
        }

        $result = @unlink($filePath);

        if (!$shouldLog) {

            return;
        }

        if ($result === true) {

            $logger->debug(sprintf('Successfully deleted cache file for %s at %s', $elementName, $filePath));

        } else {

            $logger->warn(sprintf('Could not delete cache file for %s at %s. Please delete it manually.', $elementName, $filePath));
        }
    }
}
