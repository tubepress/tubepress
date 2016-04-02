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

class tubepress_cache_api_impl_stash_FilesystemCacheBuilder
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_api_options_ContextInterface   $context,
                                tubepress_api_boot_BootSettingsInterface $bootSettings,
                                tubepress_api_log_LoggerInterface        $logger)
    {
        $this->_context      = $context;
        $this->_bootSettings = $bootSettings;
        $this->_logger       = $logger;
        $this->_shouldLog    = $logger->isEnabled();
    }

    public function buildFilesystemDriver()
    {
        $dir = $this->_context->get(tubepress_api_options_Names::CACHE_DIRECTORY);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Starting to build API cache driver. User candidate is "<code>%s</code>"', $dir));
        }

        /*
         * If a path was given, but it's not a directory, let's try to create it.
         */
        if ($dir && !is_dir($dir)) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('"<code>%s</code>" is not a directory. Let\'s try to create it...', $dir));
            }

            @mkdir($dir, 0755, true);
        }

        /*
         * If the directory exists, but isn't writable, let's try to change that.
         */
        if ($dir && is_dir($dir) && !is_writable($dir)) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('"<code>%s</code>" is a directory but we can\'t write to it. Let\'s try to change that...', $dir));
            }

            @chmod($dir, 0755);
        }

        /*
         * If we don't have a writable directory, use the system temp directory.
         */
        if (!is_dir($dir) || !is_writable($dir)) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('We don\'t have a directory that we can use for the API cache. Giving up and falling back to system directory.', $dir));
            }

            $dir = $this->_bootSettings->getPathToSystemCacheDirectory() . '/api-calls';

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Final API cache directory is "<code>%s</code>"', $dir));
            }
        }

        $driver = new \Stash\Driver\FileSystem();
        $driver->setOptions(array('path' => $dir));

        return $driver;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(API Cache Driver - Filesystem) %s', $msg));
    }
}
