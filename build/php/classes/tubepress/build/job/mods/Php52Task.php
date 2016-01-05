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

class tubepress_build_job_mods_Php52Task extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $baseDir = $this->getStagingDirectory() . '/tubepress';
        $process = $this->runProcess('grep -rl "__DIR__" .', $baseDir);
        $matches = $process->getOutput();
        $matches = preg_replace('~^\.(/.*)$~m', $baseDir . '$1', $matches);
        $matches = explode("\n", $matches);

        foreach ($matches as $file) {

            if (!is_file($file)) {

                continue;
            }

            $this->pregReplaceInFile($file, '~__DIR__~', 'dirname(__FILE__)');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'PHP 5.2 mods';
    }
}