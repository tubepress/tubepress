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
class tubepress_api_model_options_OptionDescriptorTest extends TubePressUnitTest
{
    /**
     * @var tubepress_spi_options_OptionDescriptor
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_spi_options_OptionDescriptor('name');
    }

    public function testSetAcceptableValues()
    {
        $this->assertFalse($this->_sut->hasDiscreteAcceptableValues());
        $this->_sut->setAcceptableValues(array('foo' => 'bar'));
        $this->assertTrue($this->_sut->hasDiscreteAcceptableValues());
        $this->assertTrue($this->_sut->getAcceptableValues() === array('foo' => 'bar'));
    }

    public function testSetBoolean()
    {
        $this->assertFalse($this->_sut->isBoolean());

        $this->_sut->setBoolean();

        $this->assertTrue($this->_sut->isBoolean());
    }

    public function testNonBoolShouldBePersisted()
    {
        $this->assertTrue($this->_sut->isMeantToBePersisted());

        $this->_sut->setDoNotPersist();

        $this->assertFalse($this->_sut->isMeantToBePersisted());
    }

    public function testNonBoolCanBeSetViaShortcode()
    {
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode());

        $this->_sut->setCannotBeSetViaShortcode();

        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNonStringRegex()
    {
        $this->_sut->setValidValueRegex(1);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNullRegex()
    {
        $this->_sut->setValidValueRegex(null);
    }


    public function testNonBoolProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());

        $this->_sut->setProOnly();

        $this->assertTrue($this->_sut->isProOnly());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNonStringDesc()
    {
        $this->_sut->setDescription(array());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNullDesc()
    {
        $this->_sut->setDescription(null);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNonStringLabel()
    {
        $this->_sut->setLabel(array());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNullLabel()
    {
        $this->_sut->setLabel(null);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testNonStringName()
    {
        new tubepress_spi_options_OptionDescriptor(88);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNullName()
    {
        new tubepress_spi_options_OptionDescriptor(null);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testSetRegexAlreadyBoolean()
    {
        $this->_sut->setBoolean();
        $this->_sut->setValidValueRegex('/some/');
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testSetRegexAlreadyDiscrete()
    {
        $this->_sut->setAcceptableValues(array('foo'));
        $this->_sut->setValidValueRegex('/some/');
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testSetDiscreteAlreadyBoolean()
    {
        $this->_sut->setBoolean();
        $this->_sut->setAcceptableValues(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetDiscreteAlreadyRegex()
    {
        $this->_sut->setValidValueRegex('/some/');
        $this->_sut->setAcceptableValues(array('foo'));
    }


    /**
    * @expectedException InvalidArgumentException
     */
    public function testSetBooleanAlreadyDiscrete()
    {
        $this->_sut->setAcceptableValues(array('foo'));
        $this->_sut->setBoolean();
    }

    /**
    * @expectedException InvalidArgumentException
    */
    public function testSetBooleanAlreadyRegex()
    {
        $this->_sut->setValidValueRegex('/some/');
        $this->_sut->setBoolean();
    }

    public function testGetShouldBePersisted()
    {
        $this->assertTrue(true === $this->_sut->isMeantToBePersisted());

        $this->_sut->setDoNotPersist();

        $this->assertFalse($this->_sut->isMeantToBePersisted());
    }

    public function testGetCanBeSetViaShortcode()
    {
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode());

        $this->_sut->setCannotBeSetViaShortcode();

        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode());
    }

    public function testGetRegex()
    {
        $this->assertFalse($this->_sut->hasValidValueRegex());
        $this->_sut->setValidValueRegex('regex');
        $this->assertEquals('regex', $this->_sut->getValidValueRegex());
    }

    public function testGetAliases()
    {
        $this->_sut->setAliases(array('alias'));
        $this->assertTrue(array('alias') === $this->_sut->getAliases());
    }

    public function testGetProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());

        $this->_sut->setProOnly();

        $this->assertTrue(true === $this->_sut->isProOnly());
    }

    public function testGetDescription()
    {
        $this->assertFalse($this->_sut->hasDescription());
        $this->_sut->setDescription('description');
        $this->assertEquals('description', $this->_sut->getDescription());
    }

    public function testGetName()
    {
        $this->assertEquals('name', $this->_sut->getName());
    }

    public function testGetLabel()
    {
        $this->assertFalse($this->_sut->hasLabel());
        $this->_sut->setLabel('label');
        $this->assertEquals('label', $this->_sut->getLabel());
    }

    public function testGetDefaultValue()
    {
        $this->_sut->setDefaultValue('default value');
        $this->assertEquals('default value', $this->_sut->getDefaultValue());
    }
}

