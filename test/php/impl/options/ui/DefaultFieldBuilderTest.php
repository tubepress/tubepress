<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/DefaultFieldBuilder.class.php';

class FakeThingy
{
    public $_arg;

    public function __construct($arg)
    {
        $this->_arg = $arg;
    }
}

class org_tubepress_impl_options_ui_DefaultFieldBuilderTest extends TubePressUnitTest {

	private $_sut;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_ui_DefaultFieldBuilder();
	}

	public function testBuild()
	{
        $result = $this->_sut->build('something awesome', 'FakeThingy');

        $this->assertTrue($result instanceof FakeThingy);
        $this->assertEquals('something awesome', $result->_arg);
	}
	
    public function testBuildMeta()
    {
        $this->_getFakeOds();
        
        $result = $this->_sut->buildMetaDisplayMultiSelectField();
        
        $this->assertTrue($result instanceof org_tubepress_impl_options_ui_fields_MetaMultiSelectField);
    }
    
    private function _getOdNames()
    {
        return array(
    
        org_tubepress_api_const_options_names_Meta::AUTHOR,
        org_tubepress_api_const_options_names_Meta::CATEGORY,
        org_tubepress_api_const_options_names_Meta::DESCRIPTION,
        org_tubepress_api_const_options_names_Meta::ID,
        org_tubepress_api_const_options_names_Meta::LENGTH,
        org_tubepress_api_const_options_names_Meta::LIKES,
        org_tubepress_api_const_options_names_Meta::RATING,
        org_tubepress_api_const_options_names_Meta::RATINGS,
        org_tubepress_api_const_options_names_Meta::KEYWORDS,
        org_tubepress_api_const_options_names_Meta::TITLE,
        org_tubepress_api_const_options_names_Meta::UPLOADED,
        org_tubepress_api_const_options_names_Meta::URL,
        org_tubepress_api_const_options_names_Meta::VIEWS,
        );
    }
    
    private function _getFakeOds()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
    
        $names = $this->_getOdNames();
    
        $ods = array();
    
        foreach ($names as $name) {
    
            $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
            $od->shouldReceive('isBoolean')->once()->andReturn(true);
            $od->shouldReceive('getName')->andReturn($name);
    
            $ods[] = $od;
    
            $odr->shouldReceive('findOneByName')->with($name)->once()->andReturn($od);
        }
    
        return $ods;
    }
}

