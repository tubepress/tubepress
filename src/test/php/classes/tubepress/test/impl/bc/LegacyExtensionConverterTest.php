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
 * @covers tubepress_impl_bc_LegacyExtensionConverter<extended>
 */
class tubepress_test_impl_bc_LegacyExtensionConverterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_bc_LegacyExtensionConverter
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    private $_tempFile;

    public function onSetup()
    {
        $this->_sut        = new tubepress_impl_bc_LegacyExtensionConverter();
        $this->_mockLogger = ehough_mockery_Mockery::mock('ehough_epilog_Logger');
        $this->_tempFile   = tmpfile();
    }

    public function onTearDown()
    {
        fclose($this->_tempFile);
    }

    public function testEvaluateWorks()
    {
        fwrite($this->_tempFile, "<?php class foobar{}");
        $stat = stream_get_meta_data($this->_tempFile);

        $mockAddon = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon->shouldReceive('getName')->twice()->andReturn('mockey');
        $mockAddon->shouldReceive('getClassMap')->once()->andReturn(array('foobar' => $stat['uri']));

        $this->_mockLogger->shouldReceive('debug')->once()->with('(Add-on 1 of 2: mockey) Modified class contents of foobar. Now attempting to eval().');
        $this->_mockLogger->shouldReceive('debug')->once()->with('(Add-on 1 of 2: mockey) Legacy workaround seems to have worked. Now attempting normal registration.');

        $result = $this->_sut->evaluateLegacyExtensionClass(true, $this->_mockLogger, 1, 2, $mockAddon, 'foobar');
        $this->assertTrue($result);
    }

    public function testEvaluateStillNoClass()
    {
        fwrite($this->_tempFile, "<?php \$x = 5;");
        $stat = stream_get_meta_data($this->_tempFile);

        $mockAddon = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon->shouldReceive('getName')->twice()->andReturn('mockey');
        $mockAddon->shouldReceive('getClassMap')->once()->andReturn(array('testEvaluateStillNoClass' => $stat['uri']));

        $this->_mockLogger->shouldReceive('debug')->once()->with('(Add-on 1 of 2: mockey) Modified class contents of testEvaluateStillNoClass. Now attempting to eval().');
        $this->_mockLogger->shouldReceive('warn')->once()->with('(Add-on 1 of 2: mockey) eval() succeeded, but something is still wrong. Please upgrade this add-on.');

        $result = $this->_sut->evaluateLegacyExtensionClass(true, $this->_mockLogger, 1, 2, $mockAddon, 'testEvaluateStillNoClass');
        $this->assertFalse($result);
    }

    public function testEvaluateEvalFails()
    {
        fwrite($this->_tempFile, "bla bla bla");
        $stat = stream_get_meta_data($this->_tempFile);

        $mockAddon = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon->shouldReceive('getName')->twice()->andReturn('mockey');
        $mockAddon->shouldReceive('getClassMap')->once()->andReturn(array('foobar' => $stat['uri']));

        $this->_mockLogger->shouldReceive('debug')->once()->with('(Add-on 1 of 2: mockey) Modified class contents of foobar. Now attempting to eval().');
        $this->_mockLogger->shouldReceive('warn')->once()->with('(Add-on 1 of 2: mockey) eval() of foobar failed. Please upgrade this add-on.');

        $result = $this->_sut->evaluateLegacyExtensionClass(true, $this->_mockLogger, 1, 2, $mockAddon, 'foobar');
        $this->assertFalse($result);
    }

    public function testEvaluateNoSuchFile()
    {
        $mockAddon = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon->shouldReceive('getName')->once()->andReturn('mockey');
        $mockAddon->shouldReceive('getClassMap')->once()->andReturn(array('foobar' => 'bar'));

        $this->_mockLogger->shouldReceive('warn')->once()->with('(Add-on 1 of 2: mockey) Could not read extension file. foobar will not be loaded. Please upgrade this add-on.');

        $result = $this->_sut->evaluateLegacyExtensionClass(true, $this->_mockLogger, 1, 2, $mockAddon, 'foobar');
        $this->assertFalse($result);
    }

    public function testEvaluateClassNotInMap()
    {
        $mockAddon = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon->shouldReceive('getName')->once()->andReturn('mockey');
        $mockAddon->shouldReceive('getClassMap')->once()->andReturn(array('foo' => 'bar'));

        $this->_mockLogger->shouldReceive('warn')->once()->with('(Add-on 1 of 2: mockey) Could not read extension file. foobar will not be loaded. Please upgrade this add-on.');

        $result = $this->_sut->evaluateLegacyExtensionClass(true, $this->_mockLogger, 1, 2, $mockAddon, 'foobar');
        $this->assertFalse($result);
    }

    public function testIsLegacyExtension()
    {
        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon1->shouldReceive('getName')->once()->andReturn('vimeo-all-access');
        $mockAddon1->shouldReceive('getVersion')->once()->andReturn(new tubepress_api_version_Version(1, 0, 0));

        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon2->shouldReceive('getName')->once()->andReturn('vimeo-all-access');
        $mockAddon2->shouldReceive('getVersion')->once()->andReturn(new tubepress_api_version_Version(0, 9, 9));

        $mockAddon3 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon3->shouldReceive('getName')->once()->andReturn('vimeo-all-access');
        $mockAddon3->shouldReceive('getVersion')->once()->andReturn(new tubepress_api_version_Version(2, 0, 0));

        $mockAddon4 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon4->shouldReceive('getName')->once()->andReturn('blablabla-all-access');

        $this->assertTrue($this->_sut->isLegacyAddon($mockAddon1));
        $this->assertTrue($this->_sut->isLegacyAddon($mockAddon2));
        $this->assertFalse($this->_sut->isLegacyAddon($mockAddon3));
        $this->assertFalse($this->_sut->isLegacyAddon($mockAddon4));
    }
}