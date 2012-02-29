<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/galleryinitjs/GalleryInitJsBaseParams.class.php';

class org_tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParamsTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();

		$this->_sut = new org_tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParams();
	}

	function testAlter()
	{
	    $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(true);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(999);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(888);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::FLUID_THUMBS)->andReturn(false);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-loc');
        $context->shouldReceive('toShortcode')->once()->andReturn(' + "&');

        $themeHandler = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $themeHandler->shouldReceive('calculateCurrentThemeName')->once()->andReturn('default');

        $fe           = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $fe->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('base-path');

	    $result = $this->_sut->alter_galleryInitJavaScript(array('yo' => 'mamma'));

	    $this->assertEquals(array(

	        'ajaxPagination' => 'true',
	        'embeddedHeight' => '"999"',
	        'embeddedWidth' => '"888"',
	        'fluidThumbs' => 'false',
	        'playerLocationName' => '"player-loc"',
            'shortcode' => '"%20%2B%20%22%26"',
            'themeCSS' => '""',
            'yo' => 'mamma'

	    ), $result);
	}

	function testAlterNonArray()
	{
	    $result = $this->_sut->alter_galleryInitJavaScript('yo');

	    $this->assertEquals('yo', $result);
	}
}