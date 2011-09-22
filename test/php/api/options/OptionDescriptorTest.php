<?php

require_once BASE . '/sys/classes/org/tubepress/api/options/OptionDescriptor.class.php';

class org_tubepress_api_options_OptionDescriptorTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonAssociativeArrayValueMap()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array('one'));
    }

    /**
    * @expectedException Exception
    */
    function testNonArrayValueMap()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, 88);
    }

    function testEmptyValueMap()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array());
    }

    /**
    * @expectedException Exception
    */
    function testNullValueMap()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, null);
    }

    /**
    * @expectedException Exception
    */
    function testNonBoolShouldBePersisted()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, 77, array('key' => 'value'));
    }

    /**
     * @expectedException Exception
     */
    function testNullShouldBePersisted()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, null, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonBoolCanBeSetViaShortcode()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', 88, true, array('key' => 'value'));
    }

    /**
     * @expectedException Exception
     */
    function testNullCanBeSetViaShortcode()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', null, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonStringRegex()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), 88, false, true, array('key' => 'value'));
    }

    function testNullRegex()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), array('youtube'), null, false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonArrayExcludedProviders()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), 'stuff', 'regex', false, true, array('key' => 'value'));
    }

    /**
     * @expectedException Exception
     */
    function testNullExcludedProviders()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, array('alias'), null, 'regex', false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonArrayAliases()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, 'stuff', array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
     * @expectedException Exception
     */
    function testNullAliases()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', true, null, array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonBoolProOnly()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', 'stuff', array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
     * @expectedException Exception
     */
    function testNullProOnly()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 'description', null, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonStringDesc()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', 88, true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    function testNullDesc()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 'label', 'default value', null, true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonStringLabel()
    {
        new org_tubepress_api_options_OptionDescriptor('name', 44, 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    function testNullLabel()
    {
        new org_tubepress_api_options_OptionDescriptor('name', null, 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
    * @expectedException Exception
    */
    function testNonStringName()
    {
        new org_tubepress_api_options_OptionDescriptor(88, 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    /**
     * @expectedException Exception
     */
    function testNullName()
    {
        new org_tubepress_api_options_OptionDescriptor(null, 'label', 'default value', 'description', true, array('alias'), array('youtube'), 'regex', false, true, array('key' => 'value'));
    }

    function testGetValueMap()
    {
        $this->assertTrue(array('key' => 'value') === $this->_sut->getValueMap());
    }

    function testGetShouldBePersisted()
    {
        $this->assertTrue(true === $this->_sut->isMeantToBePersisted());
    }

    function testGetCanBeSetViaShortcode()
    {
        $this->assertTrue(false === $this->_sut->isAbleToBeSetViaShortcode());
    }

    function testGetRegex()
    {
        $this->assertEquals('regex', $this->_sut->getValidValueRegex());
    }

    function testGetExcludedProviders()
    {
        $this->assertTrue($this->_sut->isApplicableToVimeo());
        $this->assertFalse($this->_sut->isApplicableToYouTube());
    }

    function testGetAliases()
    {
        $this->assertTrue(array('alias') === $this->_sut->getAliases());
    }

    function testGetProOnly()
    {
        $this->assertTrue(true === $this->_sut->isProOnly());
    }

    function testGetDescription()
    {
        $this->assertEquals('description', $this->_sut->getDescription());
    }

    function testGetName()
    {
        $this->assertEquals('name', $this->_sut->getName());
    }

    function testGetLabel()
    {
        $this->assertEquals('label', $this->_sut->getLabel());
    }

    function testGetDefaultValue()
    {
        $this->assertEquals('default value', $this->_sut->getDefaultValue());
    }
}

