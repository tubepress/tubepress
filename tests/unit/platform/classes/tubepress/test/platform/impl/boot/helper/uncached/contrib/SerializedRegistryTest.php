<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_platform_impl_boot_helper_uncached_contrib_SerializedRegistry<extended>
 */
class tubepress_test_platform_impl_boot_helper_uncached_contrib_SerializedRegistryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSerializer;

    public function onSetup()
    {
        $this->_mockLogger     = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockSerializer = $this->mock('tubepress_platform_impl_boot_helper_uncached_Serializer');
    }

    public function testUnserialize()
    {
        $mockAddon = new tubepress_internal_contrib_Addon('name', '1.2.3', 'some title', array(array('name' => 'eric hough')), array(array('url' => 'http://foo.bar/license.txt')));

        $this->_mockSerializer->shouldReceive('unserialize')->once()->with('sdf')->andReturn(array($mockAddon));

        $bootArtifacts = array('add-ons' => 'sdf');

        $result = $this->_buildSut($bootArtifacts);

        $this->assertInstanceOf('tubepress_platform_impl_boot_helper_uncached_contrib_SerializedRegistry', $result);

        $all = $result->getAll();

        $this->assertTrue(is_array($all));
        $this->assertCount(1, $all);

        $addon = $all[0];

        $this->assertInstanceOf(tubepress_platform_api_addon_AddonInterface::_, $addon);
        $this->assertEquals('name', $addon->getName());

        $this->assertSame($addon, $result->getInstanceByName('name'));
    }

    public function testUnserializeNonContribs()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unserialized data contained a non contributable');

        $this->_mockSerializer->shouldReceive('unserialize')->once()->with('x')->andReturn(array('hi'));

        $bootArtifacts = array('add-ons' => 'x');

        $this->_buildSut($bootArtifacts);
    }

    public function testUnserializeNonArray()
    {
        $this->setExpectedException('InvalidArgumentException', 'Expected to deserialize an array');

        $this->_mockSerializer->shouldReceive('unserialize')->once()->with('hello')->andReturn('yo');

        $bootArtifacts = array('add-ons' => 'hello');

        $this->_buildSut($bootArtifacts);
    }

    public function testUnserializeMissingKey()
    {
        $this->setExpectedException('InvalidArgumentException', 'add-ons not found in boot artifacts');

        $bootArtifacts = array();

        $this->_buildSut($bootArtifacts);
    }

    private function _buildSut(array $bootArtifacts)
    {
        return new tubepress_platform_impl_boot_helper_uncached_contrib_SerializedRegistry(
            $bootArtifacts,
            'add-ons',
            $this->_mockLogger,
            $this->_mockSerializer
        );
    }
}