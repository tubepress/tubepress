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

class tubepress_build_job_mods_CompressTask extends tubepress_build_job_AbstractBuildTask
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string[]
     */
    private $_relativePaths;

    public function __construct($name, array $relativePaths)
    {
        $this->_name          = $name;
        $this->_relativePaths = $relativePaths;
    }

    public function run()
    {
        $stagingDir = $this->getStagingDirectory() . '/tubepress';
        $fs         = new \Symfony\Component\Filesystem\Filesystem();

        foreach ($this->_relativePaths as $relativePath) {

            $source = sprintf('%s/%s', $stagingDir, $relativePath);
            $dest   = str_replace('.css', '-dev.css', $source);
            $dest   = str_replace('.js', '-dev.js', $dest);

            $fs->copy($source, $dest);

            $cmd = sprintf('java -jar %s %s -o %s',

                $this->getBuildDirectory() . '/../vendor/nervo/yuicompressor/yuicompressor.jar',
                $source,
                $source
            );

            $this->runProcess($cmd);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Compress ' . $this->_name;
    }
}