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

class tubepress_build_job_stage_StageSrcTask extends tubepress_build_job_AbstractBuildTask
{
    /**
     * @var string[]
     */
    private $_excludes;

    public function __construct(array $excludes)
    {
        $this->_excludes = $excludes;
    }

    public function run()
    {
        $excludesFile = stream_get_meta_data(tmpfile())['uri'];

        file_put_contents($excludesFile, implode("\n", $this->_excludes));

        $cmd = sprintf('rsync -ah --delete --delete-excluded --exclude-from=%s ../ ./stage/tubepress/', $excludesFile);

        $this->runProcess($cmd, $this->getBuildDirectory());

        unlink($excludesFile);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Stage Source';
    }
}