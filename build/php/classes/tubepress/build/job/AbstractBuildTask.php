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

abstract class tubepress_build_job_AbstractBuildTask
{
    /**
     * @return void
     */
    public abstract function run();

    /**
     * @return string
     */
    public abstract function getName();

    /**
     * @param string $message The message to log.
     *
     * @return void
     */
    protected function log($message)
    {
        printf("\t%s\n", $message);
    }

    protected function getBuildDirectory()
    {
        return realpath(__DIR__ . '/../../../../../');
    }

    protected function getStagingDirectory()
    {
        return realpath($this->getBuildDirectory()) . '/stage';
    }

    protected function getDistributionDirectory()
    {
        return realpath($this->getBuildDirectory()) . '/dist';
    }

    protected function getProjectDirectory()
    {
        return realpath($this->getBuildDirectory() . '/..');
    }

    protected function runProcess($commandLine, $workingDirectory = null)
    {
        $process = new \Symfony\Component\Process\Process(
            $commandLine, $workingDirectory
        );

        $finalCmd = $process->getCommandLine();

        $this->log(sprintf('Now running %s inside directory %s', $finalCmd, $process->getWorkingDirectory()));

        $process->run();

        if (!$process->isSuccessful()) {

            $this->log(sprintf('%s failed', $finalCmd));
            $this->log(sprintf('%s stdout:', $process->getOutput()));
            $this->log(sprintf('%s stderr:', $process->getErrorOutput()));

            throw new RuntimeException(sprintf('%s failed: %s - %s', $finalCmd, $process->getOutput(), $process->getErrorOutput()));
        }

        return $process;
    }

    protected function pregReplaceInFile($absPath, $search, $replace)
    {
        $this->log(sprintf('preg_replace %s -> %s in file %s', $search, $replace, $absPath));

        $contents    = file_get_contents($absPath);
        $newContents = preg_replace($search, $replace, $contents);

        file_put_contents($absPath, $newContents);
    }

    protected function runSedOnFile($absPath, $sedCommand)
    {
        $modifier = strpos(PHP_OS, 'Darwin') === false ? '' : "''";
        $cmd      = sprintf('sed -i %s \'%s\' %s', $modifier, $sedCommand, $absPath);

        $this->runProcess($cmd);
    }
}