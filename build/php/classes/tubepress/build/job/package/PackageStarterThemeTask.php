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

class tubepress_build_job_package_PackageStarterThemeTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $fs      = new \Symfony\Component\Filesystem\Filesystem();
        $tempDir = sys_get_temp_dir() . '/tubepress-build-' . time();
        $baseDir = $this->getStagingDirectory() . '/tubepress';

        try {

            $fs->mkdir($tempDir);

            $cpCmd  = sprintf('cp -r %s/src/add-ons/wordpress/resources/user-content-skeleton/themes/starter %s/.', $baseDir, $tempDir);
            $zipCmd = sprintf('zip -r %s/web/themes/starter-theme.zip ./starter', $baseDir);

            $this->runProcess($cpCmd);
            $this->runProcess($zipCmd, $tempDir);

        } catch (Exception $e) {

            $fs->remove($tempDir);

            throw $e;
        }

        $fs->remove($tempDir);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Package Starter Theme';
    }
}