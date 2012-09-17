<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommandTest extends tubepress_impl_feed_urlbuilding_AbstractUrlBuilderCommandTest
{
    const PRE = "/http:\/\/vimeo.com\/api\/rest\/v2\?";
    const POST = "&format=php&oauth_consumer_key=vimeokey&oauth_nonce=[a-zA-Z0-9]+&oauth_signature_method=HMAC-SHA1&oauth_timestamp=[0-9]+&oauth_version=1.0&oauth_signature=[a-zA-Z0-9%]+/";

    private $_sut;

    private $_mockExecutionContext;

    function setUp()
    {
        parent::setUp();

        $this->getMockProviderCalculator()->shouldReceive('calculateProviderOfVideoId')->zeroOrMoreTimes()->andReturn(tubepress_spi_provider_Provider::VIMEO);
        $this->getMockExecutionContext()->shouldReceive('get')->zeroOrMoreTimes()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(20);
    }

    /**
    * @expectedException RuntimeException
    */
    function testNoVimeoKeyGallery()
    {
        $this->expectOptions(array(
            tubepress_api_const_options_names_Feed::VIMEO_KEY => '',
            tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, true, '444333');

        $this->getSut()->execute($context);
    }

    /**
     * @expectedException RuntimeException
     */
    function testNoVimeoKeySingleVideo()
    {
        $this->expectOptions(array(
            tubepress_api_const_options_names_Feed::VIMEO_KEY => '',
            tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, true, '444333');

        $this->getSut()->execute($context);
    }

    /**
    * @expectedException RuntimeException
    */
    function testNoVimeoSecretGallery()
    {
        $this->expectOptions(array(
            tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_api_const_options_names_Feed::VIMEO_SECRET => ''
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, true, '444333');

        $this->getSut()->execute($context);
    }

    /**
     * @expectedException RuntimeException
     */
    function testNoVimeoSecretSingleVideo()
    {
        $this->expectOptions(array(
            tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_api_const_options_names_Feed::VIMEO_SECRET => ''
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, true, '444333');

        $this->getSut()->execute($context);
    }

    function testSingleVideoUrl()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, true, '444333');

        self::assertTrue($this->urlMatches('method=vimeo.videos.getInfo&video_id=444333', $context));
    }

    function testexecuteGroup()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
           tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'random',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random', $context));
    }

    function testExecuteCreditedTo()
    {
        $this->expectOptions(array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
            tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE => 'eric',
            tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
            tubepress_api_const_options_names_Feed::ORDER_BY => 'random',
            tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20', $context));
    }

    function testexecuteAlbum()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
           tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20', $context));
    }

    function testexecuteChannel()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
           tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20', $context));
    }


    function testexecuteSearch()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
           tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE => 'eric hough',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&full_response=true&page=1&per_page=20&sort=relevant', $context));
    }

    function testexecuteSearchWithUser()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
           tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE => 'eric hough',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'relevance',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'ehough'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant', $context));
    }

    function testexecuteAppearsIn()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
           tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'oldest',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest', $context));
    }

    function testexecuteLikes()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
           tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'rating',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked', $context));
    }

    function testexecuteUploadedBy()
    {
        $this->expectOptions(array(
           tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
           tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => 'eric',
           tubepress_api_const_options_names_Feed::ORDER_BY => 'commentCount',
           tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::buildContext(tubepress_spi_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented', $context));
    }

    private function urlMatches($url, $context)
    {
        $status = $this->getSut()->execute($context);

        self::assertTrue($status);

        $full = $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_URL);

        $pattern = self::PRE . $url . self::POST;
        $result = 1 === preg_match($pattern, $full);
        if (!$result) {
            echo "\n\n$full\n    does not match\n$pattern\n\n";
        }
        return $result;
    }

    protected function buildSut()
    {
        return new tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommand();
    }
}


