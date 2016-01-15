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

class tubepress_build_job_stage_StageWordPressTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        $this->_copyUserContentSkeleton($fs);
        $this->_moveWordPressRootResources($fs);
    }

    private function _copyUserContentSkeleton(\Symfony\Component\Filesystem\Filesystem $fs)
    {
        $stagingDir = $this->getStagingDirectory();
        $buildDir   = $this->getBuildDirectory();
        $source     = sprintf('%s/resources/user-content-skeleton/*', $buildDir);
        $target     = sprintf('%s/tubepress/src/add-ons/wordpress/resources/user-content-skeleton', $stagingDir);
        $cmd        = sprintf('cp -r %s %s', $source, $target);

        $fs->mkdir($target);

        $this->runProcess($cmd);
    }

    private function _moveWordPressRootResources(\Symfony\Component\Filesystem\Filesystem $fs)
    {
        $stagingDir = $this->getStagingDirectory();
        $source     = sprintf('%s/tubepress/src/add-ons/wordpress/resources/root-skeleton/*', $stagingDir);
        $target     = sprintf('%s/tubepress', $stagingDir);
        $cmd        = sprintf('cp -r %s %s', $source, $target);

        $this->runProcess($cmd);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Stage WordPress';
    }
}