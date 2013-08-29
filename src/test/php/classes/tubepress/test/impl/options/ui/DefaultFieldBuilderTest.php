<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class FakeThingy
{
    public $_arg;

    public function __construct($arg)
    {
        $this->_arg = $arg;
    }
}

class tubepress_test_impl_options_ui_DefaultFieldBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_DefaultFieldBuilder
     */
    private $_sut;

    /**
     * ehough_mockery_mockery_MockInterface
    */
    private $_mockPluggableFieldBuilder1;

    /**
     * ehough_mockery_mockery_MockInterface
     */
    private $_mockPluggableFieldBuilder2;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_options_ui_DefaultFieldBuilder();

        $this->_mockPluggableFieldBuilder1 = ehough_mockery_Mockery::mock(tubepress_spi_options_ui_PluggableFieldBuilder::_);
        $this->_mockPluggableFieldBuilder2 = ehough_mockery_Mockery::mock(tubepress_spi_options_ui_PluggableFieldBuilder::_);

        $this->_sut->setPluggableFieldBuilders(array($this->_mockPluggableFieldBuilder1, $this->_mockPluggableFieldBuilder2));
    }

    public function testBuildFromPluggables()
    {
        $mockField = ehough_mockery_Mockery::mock(tubepress_spi_options_ui_FieldInterface::CLASS_NAME);

        $this->_mockPluggableFieldBuilder1->shouldReceive('build')->once()->with('something awesome', 'FakeThingy2')->andReturn(null);
        $this->_mockPluggableFieldBuilder2->shouldReceive('build')->once()->with('something awesome', 'FakeThingy2')->andReturn($mockField);

        $result = $this->_sut->build('something awesome', 'FakeThingy2');

        $this->assertSame($mockField, $result);
    }

    public function testBuildUnknown()
    {
        $this->_mockPluggableFieldBuilder1->shouldReceive('build')->once()->with('something awesome', 'FakeThingy2')->andReturn(null);
        $this->_mockPluggableFieldBuilder2->shouldReceive('build')->once()->with('something awesome', 'FakeThingy2')->andReturn(null);

        $result = $this->_sut->build('something awesome', 'FakeThingy2');

        $this->assertNull($result);
    }

    public function testBuildFallback()
    {
        $this->_mockPluggableFieldBuilder1->shouldReceive('build')->once()->with('something awesome', 'FakeThingy')->andReturn(null);
        $this->_mockPluggableFieldBuilder2->shouldReceive('build')->once()->with('something awesome', 'FakeThingy')->andReturn(null);

        $result = $this->_sut->build('something awesome', 'FakeThingy');

        $this->assertTrue($result instanceof FakeThingy);
        $this->assertEquals('something awesome', $result->_arg);
    }




}
