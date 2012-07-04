<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/galleryhtml/GalleryJs.class.php';

class org_tubepress_impl_plugin_filters_galleryhtml_GalleryJsTest extends TubePressUnitTest
{
	private $_sut;

	private $_providerResult;

	function setup()
	{
		parent::setUp();

		$this->_sut = new org_tubepress_impl_plugin_filters_galleryhtml_GalleryJs();
		$this->_providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
	}

	function testAlterNonString()
	{
	    $result = $this->_sut->alter_galleryHtml(array('hello'), $this->_providerResult, 1, 'something');
	    $this->assertEquals(array('hello'), $result);
	}

	function testAlterHtml()
	{
		$ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $filterManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $filterManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::JAVASCRIPT_GALLERYINIT, array())->andReturn(array('yo' => 'mamma', 'is' => '"so fat"'));

	    $result = $this->_sut->alter_galleryHtml('hello', $this->_providerResult, 1, 'something');

	    $this->assertEquals($this->expectedAjax(), $result);
	}

	function expectedAjax()
	{
	    return <<<EOT
hello
<script type="text/javascript">
	TubePressGallery.init(gallery-id, {
		yo : mamma,
		is : "so fat"
	});
</script>
EOT;
	}
}