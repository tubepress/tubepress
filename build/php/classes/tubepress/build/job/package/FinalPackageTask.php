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

class tubepress_build_job_package_FinalPackageTask extends tubepress_build_job_AbstractBuildTask
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
        $distDir  = $this->getDistributionDirectory();
        $stageDir = $this->getStagingDirectory();

        $cmd = sprintf('zip -r %s/tubepress_%s_%s_%s.zip ./tubepress',
            $distDir, $this->_major, $this->_minor, $this->_micro, $stageDir);

        $this->runProcess($cmd, $stageDir);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Final Package';
    }
}