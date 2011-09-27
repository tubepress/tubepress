<?php

require_once BASE . '/sys/classes/org/tubepress/api/options/OptionDescriptor.class.php';

class org_tubepress_api_options_OptionDescriptorTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_api_options_OptionDescriptor('name');
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
    * @expectedException Exception
    */
    function testNonStringRegex()
    {
        $this->_sut->setValidValueRegex(1);
    }

    /**
    * @expectedException Exception
    */
    function testNullRegex()
    {
        $this->_sut->setValidValueRegex(null);
    }

    /**
    * @expectedException Exception
    */
    function testNonArrayExcludedProviders()
    {
        $this->_sut->setExcludedProviders(1);
    }

    /**
     * @expectedException Exception
     */
    function testNullExcludedProviders()
    {
        $this->_sut->setExcludedProviders(null);
    }

    /**
    * @expectedException Exception
    */
    function testNonArrayAliases()
    {
        $this->_sut->setAliases(1);
    }

    /**
     * @expectedException Exception
     */
    function testNullAliases()
    {
        $this->_sut->setAliases(null);
    }

    function testNonBoolProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());

        $this->_sut->setProOnly();

        $this->assertTrue($this->_sut->isProOnly());
    }

    /**
    * @expectedException Exception
    */
    function testNonStringDesc()
    {
        $this->_sut->setDescription(array());
    }

    /**
    * @expectedException Exception
    */
    function testNullDesc()
    {
        $this->_sut->setDescription(null);
    }

    /**
    * @expectedException Exception
    */
    function testNonStringLabel()
    {
        $this->_sut->setLabel(array());
    }

    /**
    * @expectedException Exception
    */
    function testNullLabel()
    {
        $this->_sut->setLabel(null);
    }

    /**
    * @expectedException Exception
    */
    function testNonStringName()
    {
        new org_tubepress_api_options_OptionDescriptor(88);
    }

    /**
     * @expectedException Exception
     */
    function testNullName()
    {
        new org_tubepress_api_options_OptionDescriptor(null);
    }

    /**
    * @expectedException Exception
    */
    function testSetRegexAlreadyBoolean()
    {
        $this->_sut->setBoolean();
        $this->_sut->setValidValueRegex('/some/');
    }

    /**
    * @expectedException Exception
    */
    function testSetRegexAlreadyDiscrete()
    {
        $this->_sut->setAcceptableValues(array('foo'));
        $this->_sut->setValidValueRegex('/some/');
    }

    /**
    * @expectedException Exception
    */
    function testSetDiscreteAlreadyBoolean()
    {
        $this->_sut->setBoolean();
        $this->_sut->setAcceptableValues(array('foo'));
    }

    /**
     * @expectedException Exception
     */
    function testSetDiscreteAlreadyRegex()
    {
        $this->_sut->setValidValueRegex('/some/');
        $this->_sut->setAcceptableValues(array('foo'));
    }


    /**
    * @expectedException Exception
     */
    function testSetBooleanAlreadyDiscrete()
    {
        $this->_sut->setAcceptableValues(array('foo'));
        $this->_sut->setBoolean();
    }

    /**
    * @expectedException Exception
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

    function testGetExcludedProviders()
    {
        $this->assertTrue($this->_sut->isApplicableToAllProviders());
        $this->_sut->setExcludedProviders(array('youtube'));
        $this->assertTrue($this->_sut->isApplicableToVimeo());
        $this->assertFalse($this->_sut->isApplicableToYouTube());
        $this->assertFalse($this->_sut->isApplicableToAllProviders());
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

