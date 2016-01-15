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

class tubepress_build_job_directories_DeleteDirectoriesTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        $stage = $this->getStagingDirectory();
        $dist  = $this->getDistributionDirectory();

        foreach (array($stage, $dist) as $dir) {

            if ($fs->exists($dir)) {

                $this->log(sprintf('Removing directory: %s', $dir));

                $fs->remove($dir);
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Delete Build Directories';
    }
}