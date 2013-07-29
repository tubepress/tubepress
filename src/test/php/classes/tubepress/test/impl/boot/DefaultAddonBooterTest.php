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

/**
 * @covers tubepress_impl_boot_DefaultAddonBooter<extended>
 */
class tubepress_test_impl_boot_DefaultAddonBooterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_DefaultAddonBooter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_boot_DefaultAddonBooter();
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

        $result = $this->_sut->boot(array($addon));

        $this->assertEmpty($result);
    }

    public function testBootstrapServiceWithError()
    {
        $this->createMockSingletonService('ValidBootstrapClass');

        $addon = $this->_buildMockAddon(

            array(),
            array('ValidBootstrapClass'),
            array()
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
    }

    public function testBootstrapClass()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('ValidBootstrapClass')
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
    }

    public function testBootstrapClassNoBootMethod()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('InValidBootstrapClass')
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
    }

    public function testBootstrapClassPrivateBootMethod()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('InValidBootstrapClassNonPublicBootFunction')
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
    }

    public function testBootstrapClassThrowsErrors()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('BootThrowsErrors')
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
    }

    public function testBootstrapClassNotInstantiable()
    {
        $addon = $this->_buildMockAddon(

            array(),
            array(),
            array('Countable')
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
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

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);

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

        $this->_sut->boot(array($addon));

        unlink($tempFile);

        $this->assertTrue(true);
    }

    public function testBootstrapFileNotExist()
    {
        $addon = $this->_buildMockAddon(

            array('no existo'),
            array(),
            array()
        );

        $this->_sut->boot(array($addon));

        $this->assertTrue(true);
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

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
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