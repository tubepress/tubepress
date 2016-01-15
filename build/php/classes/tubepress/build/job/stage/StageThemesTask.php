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

class tubepress_build_job_stage_StageThemesTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $fs         = new \Symfony\Component\Filesystem\Filesystem();
        $stagingDir = $this->getStagingDirectory();

        $map = array(

            sprintf('%s/tubepress/src/themes/public', $stagingDir) => sprintf('%s/tubepress/web/themes', $stagingDir),
            sprintf('%s/tubepress/src/themes/admin',  $stagingDir) => sprintf('%s/tubepress/web/admin-themes', $stagingDir),
        );

        foreach ($map as $source => $target) {

            $fs->mkdir($target);

            $cpCmd  = sprintf('cp -r %s/* %s/.', $source, $target);
            $rmCmd  = sprintf('rm -rf %s', $source);

            $this->runProcess($cpCmd);
            $this->runProcess($rmCmd);
        }

        $fs->remove(sprintf('%s/tubepress/src/themes', $stagingDir));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Stage Themes';
    }
}