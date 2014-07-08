<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require __DIR__ . '/../../vendor/autoload.php';

class tubepress_test_integration_Bootstrap
{
    private $_webserverPid;

    private $_webserverDocRoot;

    public function init()
    {
        $this->_webserverDocRoot = realpath(__DIR__ . '/fixtures/webroot');

        $this->_unstageTubePress();
        $this->_deleteSystemCache();

        $cacheDirCreated = $this->_createSystemCacheDir();

        if (!$cacheDirCreated) {

            return;
        }

        $antBuilt = $this->_runAnt();

        if (!$antBuilt) {

            return;
        }

        $stageTubePress = $this->_stageTubePress();

        if (!$stageTubePress) {

            return;
        }

        $serverStarted = $this->_startServer();

        if (!$serverStarted) {

            return;
        }

        register_shutdown_function(array($this, '__cleanup'));
    }

    public function __cleanup()
    {
        $this->_deleteSystemCache();
        $this->_unstageTubePress();
        $this->_stopServer();
    }

    private function _createSystemCacheDir()
    {
        $result = mkdir(sys_get_temp_dir() . '/tubepress-integration-test-cache');

        if ($result !== true) {

            print "Could not create system cache directory\n";
            return false;
        }

        return true;
    }

    private function _deleteSystemCache()
    {
        $this->_delTree(sys_get_temp_dir() . '/tubepress-integration-test-cache');
    }

    private function _stageTubePress()
    {
        $result = rename(
            __DIR__ . '/../../build/stage/tubepress',
            $this->_webserverDocRoot . '/tubepress'
        );

        if ($result !== true) {

            print "Failed to move TubePress directory\n";
            return false;
        };

        return true;
    }

    private function _unstageTubePress()
    {
        $this->_delTree($this->_webserverDocRoot . '/tubepress');
    }

    private function _runAnt()
    {
        $result = chdir(__DIR__ . '/../../build');

        if ($result !== true) {

            print "Could not change to build directory\n";
            return false;
        }

        print "Running Ant\n";

        exec('/usr/bin/ant quick', $output, $returnStatus);

        if ($returnStatus !== 0) {

            print "Ant build failed\n";
            return false;
        }

        return true;
    }

    private function _startServer()
    {

        // Command that starts the built-in web server
        $command = sprintf(
            'php -S localhost:54321 -t %s >/dev/null 2>&1 & echo $!',
            $this->_webserverDocRoot
        );

        // Execute the command and store the process ID
        $output = array();
        exec($command, $output, $returnStatus);

        if ($returnStatus !== 0) {

            print "Could not start webserver\n";
            return false;
        }

        $this->_webserverPid = (int) $output[0];

        echo sprintf(
                '%s - Web server started on localhost:54321 with PID %d',
                date('r'),
                $this->_webserverPid
            ) . PHP_EOL;

        return true;
    }

    private function _stopServer()
    {
        if (!$this->_webserverPid) {

            return;
        }

        echo sprintf('%s - Killing process with ID %d', date('r'), $this->_webserverPid) . PHP_EOL;
        @exec('kill ' . $this->_webserverPid);
    }

    private function _delTree($dir)
    {
        $files = @scandir($dir);

        if (!is_array($files)) {

            $files = array();
        }

        $files = array_diff($files, array('.','..'));

        foreach ($files as $file) {

            (is_dir("$dir/$file")) ? $this->_delTree("$dir/$file") : unlink("$dir/$file");
        }

        return @rmdir($dir);
    }
}

$bootstrap = new tubepress_test_integration_Bootstrap();
$bootstrap->init();