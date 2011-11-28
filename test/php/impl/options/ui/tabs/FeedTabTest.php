<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_FeedTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

    	    org_tubepress_api_const_options_names_Feed::ORDER_BY         => org_tubepress_impl_options_ui_fields_DropdownField::_,
    	    org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Feed::DEV_KEY          => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Feed::VIMEO_KEY        => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Feed::VIMEO_SECRET     => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST  => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Feed::FILTER           => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY  => org_tubepress_impl_options_ui_fields_BooleanField::_,

        );
	}

	protected function _getRawTitle()
	{
	    return 'Video Feed';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_FeedTab();
	}
}