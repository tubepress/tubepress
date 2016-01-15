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

class tubepress_build_job_stage_SuppressDirListingTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $fs         = new \Symfony\Component\Filesystem\Filesystem();
        $buildDir   = $this->getBuildDirectory();
        $stagingDir = $this->getStagingDirectory();
        $source     = sprintf('%s/resources/directory-listing-suppressor.php', $buildDir);
        $targets    = array(

            sprintf('%s/tubepress/index.php', $stagingDir),
            sprintf('%s/tubepress/web/index.php', $stagingDir),
            sprintf('%s/tubepress/src/add-ons/wordpress/resources/user-content-skeleton/index.php', $stagingDir)
        );

        foreach ($targets as $target) {

            $fs->copy($source, $target);
        }
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'Suppress Directory Listings';
    }
}