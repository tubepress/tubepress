<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_MetaTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

    	    org_tubepress_api_const_options_names_Meta::DATEFORMAT     => org_tubepress_impl_options_ui_fields_TextField::__,
    	    org_tubepress_api_const_options_names_Meta::RELATIVE_DATES => org_tubepress_impl_options_ui_fields_BooleanField::__,
    	    org_tubepress_api_const_options_names_Meta::DESC_LIMIT     => org_tubepress_impl_options_ui_fields_TextField::__,

        );
	}

	protected function _getRawTitle()
	{
	    return 'Meta Display';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_MetaTab();
	}
	
	protected function getAdditionalFields()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $fb  = $ioc->get(org_tubepress_spi_options_ui_FieldBuilder::_);
	    
	    $fb->shouldReceive('buildMetaDisplayMultiSelectField')->once()->andReturn('foobar');
	    
	    return array('foobar');
	}
}