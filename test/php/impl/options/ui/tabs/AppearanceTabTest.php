<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_AppearanceTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getWidgetArray()
	{
	    return array(

            org_tubepress_api_const_options_names_Display::THEME => org_tubepress_impl_options_ui_widgets_DropdownInput::_

        );
	}

	protected function _getRawTitle()
	{
	    return 'Appearance';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_AppearanceTab();
	}
}