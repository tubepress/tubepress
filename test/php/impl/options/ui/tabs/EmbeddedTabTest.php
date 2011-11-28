<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_EmbeddedTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

    	    org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION  => org_tubepress_impl_options_ui_fields_DropdownField::_,
    	    org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL      => org_tubepress_impl_options_ui_fields_DropdownField::_,
    	    org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT  => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH   => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Embedded::LAZYPLAY         => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR     => org_tubepress_impl_options_ui_fields_ColorField::_,
    	    org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT => org_tubepress_impl_options_ui_fields_ColorField::_,
    	    org_tubepress_api_const_options_names_Embedded::SHOW_INFO        => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Embedded::FULLSCREEN       => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY     => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Embedded::AUTOPLAY         => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Embedded::LOOP             => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Embedded::SHOW_RELATED     => org_tubepress_impl_options_ui_fields_BooleanField::_,

        );
	}

	protected function _getRawTitle()
	{
	    return 'Embedded Player';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_EmbeddedTab();
	}
}