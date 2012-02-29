<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_ThemeTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

            org_tubepress_api_const_options_names_Thumbs::THEME => org_tubepress_impl_options_ui_fields_ThemeField::__

        );
	}

	protected function _getRawTitle()
	{
	    return 'Theme';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_ThemeTab();
	}
}