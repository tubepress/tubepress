<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_api_model_options_OptionDescriptorTest extends TubePressUnitTest
{

    private $_sut;

    function onSetup()
    {
        $this->_sut = new tubepress_spi_options_OptionDescriptor('name');
    }

    function testSetAcceptableValues()
    {
        $this->assertFalse($this->_sut->hasDiscreteAcceptableValues());
        $this->_sut->setAcceptableValues(array('foo' => 'bar'));
        $this->assertTrue($this->_sut->hasDiscreteAcceptableValues());
        $this->assertTrue($this->_sut->getAcceptableValues() === array('foo' => 'bar'));
    }

    function testSetBoolean()
    {
        $this->assertFalse($this->_sut->isBoolean());

        $this->_sut->setBoolean();

        $this->assertTrue($this->_sut->isBoolean());
    }

    function testNonBoolShouldBePersisted()
    {
        $this->assertTrue($this->_sut->isMeantToBePersisted());

        $this->_sut->setDoNotPersist();

        $this->assertFalse($this->_sut->isMeantToBePersisted());
    }

    function testNonBoolCanBeSetViaShortcode()
    {
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode());

        $this->_sut->setCannotBeSetViaShortcode();

        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNonStringRegex()
    {
        $this->_sut->setValidValueRegex(1);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNullRegex()
    {
        $this->_sut->setValidValueRegex(null);
    }


    function testNonBoolProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());

        $this->_sut->setProOnly();

        $this->assertTrue($this->_sut->isProOnly());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNonStringDesc()
    {
        $this->_sut->setDescription(array());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNullDesc()
    {
        $this->_sut->setDescription(null);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNonStringLabel()
    {
        $this->_sut->setLabel(array());
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNullLabel()
    {
        $this->_sut->setLabel(null);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testNonStringName()
    {
        new tubepress_spi_options_OptionDescriptor(88);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testNullName()
    {
        new tubepress_spi_options_OptionDescriptor(null);
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testSetRegexAlreadyBoolean()
    {
        $this->_sut->setBoolean();
        $this->_sut->setValidValueRegex('/some/');
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testSetRegexAlreadyDiscrete()
    {
        $this->_sut->setAcceptableValues(array('foo'));
        $this->_sut->setValidValueRegex('/some/');
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testSetDiscreteAlreadyBoolean()
    {
        $this->_sut->setBoolean();
        $this->_sut->setAcceptableValues(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetDiscreteAlreadyRegex()
    {
        $this->_sut->setValidValueRegex('/some/');
        $this->_sut->setAcceptableValues(array('foo'));
    }


    /**
    * @expectedException InvalidArgumentException
     */
    function testSetBooleanAlreadyDiscrete()
    {
        $this->_sut->setAcceptableValues(array('foo'));
        $this->_sut->setBoolean();
    }

    /**
    * @expectedException InvalidArgumentException
    */
    function testSetBooleanAlreadyRegex()
    {
        $this->_sut->setValidValueRegex('/some/');
        $this->_sut->setBoolean();
    }

    function testGetShouldBePersisted()
    {
        $this->assertTrue(true === $this->_sut->isMeantToBePersisted());

        $this->_sut->setDoNotPersist();

        $this->assertFalse($this->_sut->isMeantToBePersisted());
    }

    function testGetCanBeSetViaShortcode()
    {
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode());

        $this->_sut->setCannotBeSetViaShortcode();

        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode());
    }

    function testGetRegex()
    {
        $this->assertFalse($this->_sut->hasValidValueRegex());
        $this->_sut->setValidValueRegex('regex');
        $this->assertEquals('regex', $this->_sut->getValidValueRegex());
    }

    function testGetAliases()
    {
        $this->_sut->setAliases(array('alias'));
        $this->assertTrue(array('alias') === $this->_sut->getAliases());
    }

    function testGetProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());

        $this->_sut->setProOnly();

        $this->assertTrue(true === $this->_sut->isProOnly());
    }

    function testGetDescription()
    {
        $this->assertFalse($this->_sut->hasDescription());
        $this->_sut->setDescription('description');
        $this->assertEquals('description', $this->_sut->getDescription());
    }

    function testGetName()
    {
        $this->assertEquals('name', $this->_sut->getName());
    }

    function testGetLabel()
    {
        $this->assertFalse($this->_sut->hasLabel());
        $this->_sut->setLabel('label');
        $this->assertEquals('label', $this->_sut->getLabel());
    }

    function testGetDefaultValue()
    {
        $this->_sut->setDefaultValue('default value');
        $this->assertEquals('default value', $this->_sut->getDefaultValue());
    }
}

