<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/galleryhtml/GalleryJs.class.php';

class org_tubepress_impl_plugin_filters_galleryhtml_GalleryJsTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_galleryhtml_GalleryJs();

		$ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION)->andReturn(true);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME)->andReturn('current-player-name');
        $context->shouldReceive('getShortcode')->once()->andReturn('shortcode');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::FLUID_THUMBS)->andReturn(false);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(500);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(800);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_ExecutionContextVariables::GALLERY_ID)->andReturn('gallery-id');

        $themeHandler = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $themeHandler->shouldReceive('calculateCurrentThemeName')->once()->andReturn('current-theme-name');
        $themeHandler->shouldReceive('getCssPath')->once()->with('current-theme-name')->andReturn('css-path');

        $fe           = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fe->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('baseInstallPath');
	}

	function testAlter()
	{
	    $result = $this->_sut->alter_galleryHtml('hello', new org_tubepress_api_provider_ProviderResult(), 1, 'provider-name');
	    $this->assertEquals($this->expectedAjax(), $result);
	}

	function expectedAjax()
	{
	    return <<<EOT
hello<script type="text/javascript">
	TubePressGallery.init(gallery-id, {
		ajaxPagination: true,
		fluidThumbs: false,
		shortcode: "shortcode",
		playerLocationName: "current-player-name",
		embeddedHeight: "500",
		embeddedWidth: "800",
		themeCSS: ""
    });
</script>
EOT;
	}
}