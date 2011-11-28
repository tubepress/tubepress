<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_ThumbsTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

            org_tubepress_api_const_options_names_Thumbs::THEME            => org_tubepress_impl_options_ui_fields_ThemeField::_,
            org_tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT     => org_tubepress_impl_options_ui_fields_TextField::_,
            org_tubepress_api_const_options_names_Thumbs::THUMB_WIDTH      => org_tubepress_impl_options_ui_fields_TextField::_,
            org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION  => org_tubepress_impl_options_ui_fields_BooleanField::_,
            org_tubepress_api_const_options_names_Thumbs::FLUID_THUMBS     => org_tubepress_impl_options_ui_fields_BooleanField::_,
            org_tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE   => org_tubepress_impl_options_ui_fields_BooleanField::_,
            org_tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW   => org_tubepress_impl_options_ui_fields_BooleanField::_,
            org_tubepress_api_const_options_names_Thumbs::HQ_THUMBS        => org_tubepress_impl_options_ui_fields_BooleanField::_,
            org_tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS    => org_tubepress_impl_options_ui_fields_BooleanField::_,
            org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => org_tubepress_impl_options_ui_fields_TextField::_,

        );
	}

	protected function _getRawTitle()
	{
	    return 'Thumbnails';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_ThumbsTab();
	}
}