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

class tubepress_build_Builder
{
    /**
     * @var array[]
     */
    private $_targets;

    public function __construct(array $targets)
    {
        foreach ($targets as $target => $tasks) {

            foreach ($tasks as $task)

            if (!($task instanceof tubepress_build_job_AbstractBuildTask)) {

                throw new InvalidArgumentException('Non tubepress_build_job_AbstractBuildTask passed to builder');
            }
        }

        $this->_targets = $targets;
    }

    public function build()
    {
        global $argc, $argv;

        /** @noinspection PhpUndefinedVariableInspection */
        if ($argc === 1) {

            $target = 'default';

        } else {

            /** @noinspection PhpUndefinedVariableInspection */
            $target = trim($argv[1]);
        }

        if (!isset($this->_targets[$target])) {

            throw new InvalidArgumentException(sprintf("No such target: %s", $target));
        }

        /**
         * @var $tasks tubepress_build_job_AbstractBuildTask[]
         */
        $tasks     = $this->_targets[$target];
        $taskCount = count($tasks);

        printf("Running \"%s\" target with %d task(s):\n\n", $target, $taskCount);

        for ($x = 0; $x < $taskCount; $x++) {

            printf("\t%d: %s\n",($x + 1), $tasks[$x]->getName());
        }

        printf("\n");

        for ($x = 0; $x < $taskCount; $x++) {

            $task = $tasks[$x];

            printf("Now running task %d of %d: \"%s\"\n\n", ($x + 1), $taskCount, $task->getName());

            $task->run();

            printf("\nDone running task %d of %d: \"%s\"\n\n", ($x + 1), $taskCount, $task->getName());
        }
    }
}