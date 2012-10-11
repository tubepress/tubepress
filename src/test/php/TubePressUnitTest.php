<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of epilog (https://github.com/ehough/epilog)
 *
 * epilog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * epilog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

abstract class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    public final function tearDown()
    {
        $this->onTearDown();

        tubepress_impl_patterns_ioc_KernelServiceLocator::reset();

        Mockery::close();
    }

    public static function setUpBeforeClass()
    {
        if (! defined('TUBEPRESS_ROOT')) {

            define('TUBEPRESS_ROOT', realpath(__DIR__ . '/../../../'));
        }
    }

    protected function onTearDown()
    {
        //override point
    }
}