<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_AdvancedTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

    	    org_tubepress_api_const_options_names_Advanced::DEBUG_ON               => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Advanced::KEYWORD                => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL      => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP   => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN     => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS   => org_tubepress_impl_options_ui_fields_BooleanField::_,            

        );
	}

	protected function _getRawTitle()
	{
	    return 'Advanced';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_AdvancedTab();
	}
}