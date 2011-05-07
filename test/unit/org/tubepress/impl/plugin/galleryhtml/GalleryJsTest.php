<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/plugin/galleryhtml/GalleryJs.class.php';

class org_tubepress_impl_plugin_galleryhtml_GalleryJsTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_plugin_galleryhtml_GalleryJs();
	}

	function testAjaxPagination()
	{
	    $this->setOptions(array(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION => true));
	    $result = $this->_sut->alter_galleryHtml('hello', 'galleryid');
	    $this->assertEquals($this->expectedAjax(), $result);
	}
	
	function testNonAjaxPagination()
	{
	    $this->assertEquals($this->expectedNonAjax(), $this->_sut->alter_galleryHtml('hello', 'galleryid'));
	}
	
	function testFilterNonString()
	{
	    $result = $this->_sut->alter_galleryHtml(array(), 3);
	    $this->assertTrue(is_array($result));
	    $this->assertEquals(0, count($result));
	}

	function expectedNonAjax()
	{
	    return <<<EOT
hello<script type="text/javascript">
	TubePressGallery.init(galleryid, {
		ajaxPagination: false,
		fluidThumbs: true,
		shortcode: "",
		playerLocationName: "normal",
		embeddedHeight: "350",
		embeddedWidth: "425",
		themeCSS: "default"
    });
</script>
EOT;
	}
	
	function expectedAjax()
	{
	    return <<<EOT
hello<script type="text/javascript">
	TubePressGallery.init(galleryid, {
		ajaxPagination: true,
		fluidThumbs: true,
		shortcode: "",
		playerLocationName: "normal",
		embeddedHeight: "350",
		embeddedWidth: "425",
		themeCSS: "default"
    });
</script>
EOT;
	}

}

