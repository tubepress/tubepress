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

class tubepress_build_job_deploy_DeployTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        global $argv;

        if (!isset($argv[2])) {

            $this->log('No deployment directory defined');

            return;
        }

        $targetDir = $argv[2];

        if (!is_dir($targetDir)) {

            throw new RuntimeException('Invalid deployment directory');
        }

        $source = sprintf('%s/tubepress/', $this->getStagingDirectory());
        $target = rtrim($targetDir, '/') . '/';

        $cmd = sprintf('rsync -ah --delete %s %s', $source, $target);

        $this->runProcess($cmd);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Deploy';
    }
}