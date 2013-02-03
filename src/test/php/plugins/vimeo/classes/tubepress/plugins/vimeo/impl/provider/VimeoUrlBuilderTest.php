<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilderTest extends TubePressUnitTest
{
    const PRE = "/http:\/\/vimeo.com\/api\/rest\/v2\?";
    const POST = "&format=php&oauth_consumer_key=vimeokey&oauth_nonce=[a-zA-Z0-9]+&oauth_signature_method=HMAC-SHA1&oauth_timestamp=[0-9]+&oauth_version=1.0&oauth_signature=[a-zA-Z0-9%]+/";

    /**
     * @var tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilder
     */
    private $_sut;

    private $_mockExecutionContext;

    function onSetup()
    {
        $this->_sut = new tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilder();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
    }

    /**
    * @expectedException RuntimeException
    */
    function testNoVimeoKeyGallery()
    {
        $this->expectOptions(array(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => '',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->buildGalleryUrl(3);
    }

    /**
     * @expectedException RuntimeException
     */
    function testNoVimeoKeySingleVideo()
    {
        $this->expectOptions(array(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => '',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $this->_sut->buildSingleVideoUrl('xyz');
    }

    /**
    * @expectedException RuntimeException
    */
    function testNoVimeoSecretGallery()
    {
        $this->expectOptions(array(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => ''
        ));

        $this->_sut->buildGalleryUrl(3);
    }

    /**
     * @expectedException RuntimeException
     */
    function testNoVimeoSecretSingleVideo()
    {
        $this->expectOptions(array(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => ''
        ));

        $this->_sut->buildSingleVideoUrl('xyz');
    }

    function testSingleVideoUrl()
    {
        $this->expectOptions(array(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildSingleVideoUrl('444333');
        
        $this->assertTrue($this->urlMatches('method=vimeo.videos.getInfo&video_id=444333', $result));
    }

    function testexecuteGroup()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'random',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random', $result));
    }

    function testExecuteCreditedTo()
    {
        $this->expectOptions(array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE => 'eric',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_api_const_options_names_Feed::ORDER_BY => 'random',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20', $result));
    }

    function testexecuteAlbum()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE => 'eric',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20', $result));
    }

    function testexecuteChannel()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE => 'eric',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20', $result));
    }


    function testexecuteSearch()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE => 'eric hough',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&full_response=true&page=1&per_page=20&sort=relevant', $result));
    }

    function testexecuteSearchWithUser()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE => 'eric hough',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'ehough'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant', $result));
    }

    function testexecuteAppearsIn()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'oldest',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest', $result));
    }

    function testexecuteLikes()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'rating',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked', $result));
    }

    function testexecuteUploadedBy()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
           tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'commentCount',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $result = $this->_sut->buildGalleryUrl(1);

        $this->assertTrue($this->urlMatches('method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented', $result));
    }

    private function urlMatches($url, $full)
    {
        $pattern = self::PRE . $url . self::POST;
        $result = 1 === preg_match($pattern, $full);
        if (!$result) {
            echo "\n\n$full\n    does not match\n$pattern\n\n";
        }
        return $result;
    }

    private function expectOptions($arr) {

        foreach ($arr as $key => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->with($key)->andReturn($value);
        }
    }
}


