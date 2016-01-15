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

class tubepress_build_job_l10n_LocaleFallbacksTask extends tubepress_build_job_AbstractBuildTask
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
        $this->_workingDirectory = $this->getStagingDirectory() . '/tubepress/src/translations';
        $this->_localeMap        = $localeMap;
    }

    public function run()
    {
        $fmt = '%s/tubepress-%s.mo';

        foreach ($this->_localeMap as $originalLocale => $fallbackLocale) {

            $source = sprintf($fmt, $this->_workingDirectory, $originalLocale);
            $target = sprintf($fmt, $this->_workingDirectory, $fallbackLocale);

            $this->log(sprintf('Copying %s to %s', $source, $target));

            $this->_filesystem->copy($source, $target);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Locale Fallbacks';
    }

}