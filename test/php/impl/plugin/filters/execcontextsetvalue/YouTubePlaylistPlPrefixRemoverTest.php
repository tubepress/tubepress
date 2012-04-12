<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/execcontextsetvalue/YouTubePlaylistPlPrefixRemover.class.php';

class org_tubepress_impl_plugin_filters_execcontextsetvalue_YouTubePlaylistPlPrefixRemoverTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();

		$this->_sut = new org_tubepress_impl_plugin_filters_execcontextsetvalue_YouTubePlaylistPlPrefixRemover();
	}

	function testAlterNonString()
	{
	    $result = $this->_sut->alter_preValidationOptionSet(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE, array('hello'));
	    $this->assertEquals(array('hello'), $result);
	}

	function testAlterHtmlNonPrefix()
	{
	    $result = $this->_sut->alter_preValidationOptionSet(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE, 'hello');
	    $this->assertEquals('hello', $result);
	}

	function testAlterPrefix()
	{
	    $result = $this->_sut->alter_preValidationOptionSet(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE, 'PLhelloPL');
	    $this->assertEquals('helloPL', $result);
	}
}