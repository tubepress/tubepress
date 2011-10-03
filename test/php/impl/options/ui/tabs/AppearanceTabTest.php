<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_AppearanceTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getWidgetArray()
	{
	    return array(

            org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_impl_options_ui_widgets_DropdownInput::_,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE    => org_tubepress_impl_options_ui_widgets_TextInput::_,
            org_tubepress_api_const_options_names_Display::FLUID_THUMBS        => org_tubepress_impl_options_ui_widgets_BooleanInput::_,
            org_tubepress_api_const_options_names_Display::THUMB_HEIGHT        => org_tubepress_impl_options_ui_widgets_TextInput::_,
            org_tubepress_api_const_options_names_Display::THUMB_WIDTH         => org_tubepress_impl_options_ui_widgets_TextInput::_,
            org_tubepress_api_const_options_names_Display::THEME               => org_tubepress_impl_options_ui_widgets_DropdownInput::_,

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