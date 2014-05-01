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
 * @covers tubepress_addons_coreapiservices_impl_options_Provider<extended>
 */
class tubepress_test_addons_coreapiservices_impl_options_ProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_coreapiservices_impl_options_Provider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider2;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockOptionProviders;

    /**
     * @var string[]
     */
    private $_mockOptionNames = array('one', 'two', 'blue');

    public function onSetup()
    {
        $this->_mockOptionProvider1 = ehough_mockery_Mockery::mock(tubepress_api_options_ProviderInterface::_);
        $this->_mockOptionProvider2 = ehough_mockery_Mockery::mock(tubepress_api_options_ProviderInterface::_);

        $this->_mockOptionProviders = array(

            $this->_mockOptionProvider1,
            $this->_mockOptionProvider2,
        );

        $this->_sut = new tubepress_addons_coreapiservices_impl_options_Provider();
        $this->_sut->setAddonOptionProviders($this->_mockOptionProviders);
        $this->_sut->setRegisteredOptionNames($this->_mockOptionNames);
    }

    public function testGetAllOptionNames()
    {
        $actual = $this->_sut->getAllOptionNames();

        $this->assertEquals($this->_mockOptionNames, $actual);
    }

    public function testGetDiscreteAcceptableValues()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('getDiscreteAcceptableValues')->once()->with('one')->andReturn(array('x', 'y', 'z'));

        $actual = $this->_sut->getDiscreteAcceptableValues('one');

        $this->assertEquals(array('x', 'y', 'z'), $actual);
    }

    public function testGetDefaultValue()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('getDefaultValue')->once()->with('one')->andReturn(array('x', 'y', 'z'));

        $actual = $this->_sut->getDefaultValue('one');

        $this->assertEquals(array('x', 'y', 'z'), $actual);
    }

    public function testGetDescription()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('getDescription')->once()->with('one')->andReturn(array('x', 'y', 'z'));

        $actual = $this->_sut->getDescription('one');

        $this->assertEquals(array('x', 'y', 'z'), $actual);
    }

    public function testGetLabel()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('getLabel')->once()->with('one')->andReturn(array('x', 'y', 'z'));

        $actual = $this->_sut->getLabel('one');

        $this->assertEquals(array('x', 'y', 'z'), $actual);
    }

    public function testGetProblemMessage()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('getProblemMessage')->once()->with('one', 'foo')->andReturn(array('x', 'y', 'z'));

        $actual = $this->_sut->getProblemMessage('one', 'foo');

        $this->assertEquals(array('x', 'y', 'z'), $actual);
    }

    public function testShortcodeSettable()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('isAbleToBeSetViaShortcode')->once()->with('one')->andReturn(false);

        $actual = $this->_sut->isAbleToBeSetViaShortcode('one');

        $this->assertFalse($actual);
    }

    public function testBoolean()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('isBoolean')->once()->with('one')->andReturn(false);

        $actual = $this->_sut->isBoolean('one');

        $this->assertFalse($actual);
    }

    public function testProOnly()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('isProOnly')->once()->with('one')->andReturn(true);

        $actual = $this->_sut->isProOnly('one');

        $this->assertTrue($actual);
    }

    public function testShouldBePersisted()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('isMeantToBePersisted')->once()->with('one')->andReturn(true);

        $actual = $this->_sut->isMeantToBePersisted('one');

        $this->assertTrue($actual);
    }

    public function testIsValid()
    {
        $this->_mockOptionProvider1->shouldReceive('hasOption')->once()->with('one')->andReturn(false);
        $this->_mockOptionProvider2->shouldReceive('hasOption')->once()->with('one')->andReturn(true);
        $this->_mockOptionProvider2->shouldReceive('isValid')->once()->with('one', 'bar')->andReturn(true);

        $actual = $this->_sut->isValid('one', 'bar');

        $this->assertTrue($actual);
    }

    public function testHasOption()
    {
        $this->assertFalse($this->_sut->hasOption('three'));
        $this->assertTrue($this->_sut->hasOption('one'));
    }
}