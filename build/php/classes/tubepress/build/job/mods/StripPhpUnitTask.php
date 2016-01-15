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

class tubepress_build_job_mods_StripPhpUnitTask extends tubepress_build_job_AbstractBuildTask
{
    public function run()
    {
        $baseDir = $this->getStagingDirectory() . '/tubepress';
        $files   = array(
            sprintf('%s/vendor/composer/autoload_classmap.php', $baseDir),
        );

        foreach ($files as $file) {

            $this->runSedOnFile($file, '/\/phpunit\//d');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Strip PHPUnit';
    }
}