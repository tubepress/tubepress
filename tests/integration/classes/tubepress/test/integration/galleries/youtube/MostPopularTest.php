<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_integration_galleries_youtube_MostPopularTest extends tubepress_test_integration_IntegrationTest
{
    /**
     * @dataProvider getAllThemes
     */
    public function test8Thumbs($theme)
    {
        $this->setOptions(array(
            tubepress_api_options_Names::GALLERY_SOURCE =>
                tubepress_youtube3_api_Constants::GALLERYSOURCE_YOUTUBE_MOST_POPULAR,
            tubepress_youtube3_api_Constants::OPTION_YOUTUBE_MOST_POPULAR_VALUE =>
                tubepress_youtube3_api_Constants::TIMEFRAME_TODAY,
            tubepress_api_options_Names::THEME => $theme,
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE => 13,
        ));

        $result = $this->get();

        $this->assertNotEmpty($result);

        $this->assertThumbnailCount($result, 13, $theme);
    }

    public function assertThumbnailCount($html, $expected, $themeName)
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

        $this->assertCount($expected, $crawler->filter('html > body ' . $this->_getThumbnailSelectorForTheme($themeName)));
    }

    private function _getThumbnailSelectorForTheme($themeName)
    {
        if (strpos($themeName, '/') !== false) {

//            return 'div.js-tubepress-gallery > div.tubepress-pagination-and-thumbs > div.tubepress-thumbs > div.tubepress-thumb > a.js-tubepress-invoker > ';
            return 'div.js-tubepress-gallery > div.tubepress-pagination-and-thumbs > div.tubepress-thumbs > div.tubepress-thumb img.tubepress-thumbnail-image';
        }

        return 'div.tubepress_thumb img';
    }

    public function getAllThemes()
    {
        return array(

            array('tubepress/default'),
            array('tubepress/youtube.com-clone'),
            array('tubepress/vimeo.com-clone'),
            array('default'),
            array('sidebar'),
            array('vimeo'),
            array('youtube')
        );
    }
}