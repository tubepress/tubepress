<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/prevalidationoptionset/YouTubePlaylistPlPrefixRemover.class.php';

class org_tubepress_impl_plugin_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemoverTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();

		$this->_sut = new org_tubepress_impl_plugin_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover();
	}

	function testAlterDifferentVariable()
	{
	    $result = $this->_sut->alter_preValidationOptionSet('PLsomething', org_tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
	    $this->assertEquals('PLsomething', $result);
	}

	function testAlterNonString()
	{
	    $result = $this->_sut->alter_preValidationOptionSet(array('hello'), org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
	    $this->assertEquals(array('hello'), $result);
	}

	function testAlterHtmlNonPrefix()
	{
	    $result = $this->_sut->alter_preValidationOptionSet('hello', org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
	    $this->assertEquals('hello', $result);
	}

	function testAlterPrefix()
	{
	    $result = $this->_sut->alter_preValidationOptionSet('PLhelloPL', org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
	    $this->assertEquals('helloPL', $result);
	}
}