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

class tubepress_build_job_mods_SetRealVersionTask extends tubepress_build_job_AbstractBuildTask
{
    /**
     * @var int
     */
    private $_major;

    /**
     * @var int
     */
    private $_minor;

    /**
     * @var int
     */
    private $_micro;

    public function __construct($major, $minor, $micro)
    {
        $this->_major = $major;
        $this->_minor = $minor;
        $this->_micro = $micro;
    }

    public function run()
    {
        $baseDir = $this->getStagingDirectory() . '/tubepress';
        $process = $this->runProcess('grep -rl "99\.99\.99" .', $baseDir);
        $matches = $process->getOutput();
        $matches = preg_replace('~^\.(/.*)$~m', $baseDir . '$1', $matches);
        $matches = explode("\n", $matches);

        foreach ($matches as $file) {

            if (!is_file($file)) {

                continue;
            }

            $this->pregReplaceInFile($file, '~99\.99\.99~', sprintf('%s.%s.%s', $this->_major, $this->_minor, $this->_micro));
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Set Real Version';
    }
}