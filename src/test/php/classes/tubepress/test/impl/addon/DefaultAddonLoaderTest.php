<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_player_DefaultAddonLoaderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_DefaultAddonLoader
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_addon_DefaultAddonLoader();
    }

    public function testBootstrapService()
    {
        $mock = $this->createMockSingletonService('ValidBootstrapClass');
        $mock->shouldReceive('boot')->once();

        $addon = $this->_buildMockAddon(

            array(),
            array('ValidBootstrapClass'),
            array()
        );

        $result = $this->_sut->load($addon);

        $this->assertEmpty($result);
    }

    public function testBootstrapServiceWithError()
    {
        $mock = $this->createMockSingletonService('ValidBootstrapClass');

        $addon = $this->_buildMockAddon(

            array(),
            array('ValidBootstrapClass'),
            array()
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertEquals('Caught exception when calling boot() on ValidBootstrapClass for add-on fooey: Method ValidBootstrapClass::boot() does not exist on this mock object', $result[0]);
    }

    public function testBootstrapClass()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('ValidBootstrapClass')
        );

        $result = $this->_sut->load($addon);

        $this->assertEmpty($result);
    }

    public function testBootstrapClassNoBootMethod()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('InValidBootstrapClass')
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertEquals('InValidBootstrapClass has no boot() method', $result[0]);
    }

    public function testBootstrapClassPrivateBootMethod()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('InValidBootstrapClassNonPublicBootFunction')
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertEquals('InValidBootstrapClassNonPublicBootFunction\'s boot() method is not public', $result[0]);
    }

    public function testBootstrapClassThrowsErrors()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('BootThrowsErrors')
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertEquals('Caught exception when calling boot() on BootThrowsErrors for add-on fooey: some error', $result[0]);
    }

    public function testBootstrapClassNotInstantiable()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('Countable')
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertEquals('Countable is not instantiable', $result[0]);
    }

    public function testBootstrapFileThrowsException()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tubepress-testBootstrapThrowsException');
        $handle = fopen($tempFile, 'w');
        fwrite($handle, '<?php throw new Exception("Hi");');
        fclose($handle);

        $addon = $this->_buildMockAddon(

            array($tempFile),
            array(),
            array()
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertRegExp('~^Failed to include [^\s]+/tubepress-testBootstrapThrowsException[^:]+: Hi$~', $result[0]);

        unlink($tempFile);
    }

    public function testBootstrapFile()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tubepress-testLoadGoodAddon');

        $addon = $this->_buildMockAddon(

            array($tempFile),
            array(),
            array()
        );

        $result = $this->_sut->load($addon);

        $this->assertEmpty($result);
    }

    public function testBootstrapFileNotExist()
    {
        $addon = $this->_buildMockAddon(

            array('no existo'),
            array(),
            array()
        );

        $result = $this->_sut->load($addon);

        $this->assertTrue(count($result) === 1);
        $this->assertEquals('no existo is not a readable file', $result[0]);
    }

    private function _buildMockAddon(array $files, array $services, array $classes)
    {
        $addon = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);

        $addon->shouldReceive('getName')->andReturn('fooey');
        $addon->shouldReceive('getBootstrapFiles')->once()->andReturn($files);
        $addon->shouldReceive('getBootstrapServices')->once()->andReturn($services);
        $addon->shouldReceive('getBootstrapClasses')->once()->andReturn($classes);

        return $addon;
    }
}

class InValidBootstrapClassNonPublicBootFunction
{
    private function boot() {}
}

class InValidBootstrapClass {}

class ValidBootstrapClass
{
    public function boot() {}
}

class BootThrowsErrors
{
    public function boot()
    {
        throw new RuntimeException('some error');
    }
}