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

abstract class tubepress_test_TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public final function setUp()
    {
        date_default_timezone_set('America/New_York');

        error_reporting(E_ALL);

        $this->onSetup();
    }

    public final function tearDown()
    {
        $this->onTearDown();

        ehough_mockery_Mockery::close();
    }

    public static function setUpBeforeClass()
    {
        if (! defined('TUBEPRESS_ROOT')) {

            define('TUBEPRESS_ROOT', realpath(__DIR__ . '/../../../../../'));
        }
    }

    protected function onSetup()
    {
        //override point
    }

    protected function onTearDown()
    {
        //override point
    }

    /**
     * @param $name
     *
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function mock($name)
    {
        return ehough_mockery_Mockery::mock($name);
    }

    protected function recursivelyDeleteDirectory($dir)
    {
        if (!is_dir($dir)) {

            return;
        }

        $objects = scandir($dir);

        foreach ($objects as $object) {

            if ($object != '.' && $object != '..') {

                $x = $dir . DIRECTORY_SEPARATOR . $object;

                if (filetype($x) == 'dir') {

                    $this->recursivelyDeleteDirectory($x);

                } else  {

                    unlink($x);
                }
            }
        }

        reset($objects);
        rmdir($dir);
    }
}