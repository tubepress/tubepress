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

class tubepress_build_job_stage_StageWebAssetsTask extends tubepress_build_job_AbstractBuildTask
{
    /**
     * @var string[]
     */
    private $_relativeWebAssets;

    public function __construct(array $relativeWebAssets)
    {
        $this->_relativeWebAssets = $relativeWebAssets;
    }

    public function run()
    {
        $target = $this->getStagingDirectory() . '/tubepress/web/';

        foreach ($this->_relativeWebAssets as $relativeWebAsset) {

            $source = $this->getStagingDirectory() . '/tubepress/' . $relativeWebAsset;
            $cpCmd  = sprintf('cp -r %s/* %s', $source, $target);
            $rmCmd  = sprintf('rm -rf %s', $source);

            $this->runProcess($cpCmd);
            $this->runProcess($rmCmd);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Stage Web Assets';
    }
}