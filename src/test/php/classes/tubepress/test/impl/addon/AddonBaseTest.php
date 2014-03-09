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

/**
 * @covers tubepress_impl_addon_AddonBase
 */
class tubepress_test_impl_player_AddonBaseTest extends tubepress_test_TubePressUnitTest
{
    public function testSetPsr0NonString()
    {
        $this->setExpectedException('InvalidArgumentException', 'PSR-0 classpath roots must be strings');

        $addon = $this->_buildValidAddon();

        $addon->setPsr0ClassPathRoots(array(array()));
        $this->assertEquals(array(array()), $addon->getPsr0ClassPathRoots());
    }

    public function testSetPsr0()
    {
        $addon = $this->_buildValidAddon();

        $addon->setPsr0ClassPathRoots(array('foo'));
        $this->assertEquals(array('foo'), $addon->getPsr0ClassPathRoots());
    }

    public function testSetIocContainerCompilerPassesNonString()
    {
        $this->setExpectedException('InvalidArgumentException', 'IoC container compiler passes must be strings');

        $addon = $this->_buildValidAddon();

        $addon->setIocContainerCompilerPasses(array(array()));
        $this->assertEquals(array(array()), $addon->getIocContainerCompilerPasses());
    }

    public function testSetIocContainerCompilerPasses()
    {
        $addon = $this->_buildValidAddon();

        $addon->setIocContainerCompilerPasses(array('foo'));
        $this->assertEquals(array('foo'), $addon->getIocContainerCompilerPasses());
    }

    public function testSetIocContainerExtensionsNonString()
    {
        $this->setExpectedException('InvalidArgumentException', 'IoC container extensions must be strings');

        $addon = $this->_buildValidAddon();

        $addon->setIocContainerExtensions(array(array()));
        $this->assertEquals(array(array()), $addon->getIocContainerExtensions());
    }

    public function testSetIocContainerExtensions()
    {
        $addon = $this->_buildValidAddon();

        $addon->setIocContainerExtensions(array('foo'));
        $this->assertEquals(array('foo'), $addon->getIocContainerExtensions());
    }

    private function _buildValidAddon()
    {
        return new tubepress_impl_addon_AddonBase(

            'name',
            tubepress_spi_version_Version::parse('2.3.1'),
            'description',
            array('name' => 'eric', 'url' => 'http://foo.bar'),
            array(array('url' => 'http://foo.bar'))
        );
    }
}