<?php

require_once 'AbstractTabTest.php';

class org_tubepress_impl_options_ui_tabs_CacheTabTest extends org_tubepress_impl_options_ui_tabs_AbstractTabTest {

	protected function _getFieldArray()
	{
	    return array(

    	    org_tubepress_api_const_options_names_Cache::CACHE_ENABLED          => org_tubepress_impl_options_ui_fields_BooleanField::_,
    	    org_tubepress_api_const_options_names_Cache::CACHE_DIR              => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS => org_tubepress_impl_options_ui_fields_TextField::_,
    	    org_tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR     => org_tubepress_impl_options_ui_fields_TextField::_,

        );
	}

	protected function _getRawTitle()
	{
	    return 'Cache';
	}

	protected function _buildSut()
	{
	    return new org_tubepress_impl_options_ui_tabs_CacheTab();
	}
}