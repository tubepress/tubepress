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
class tubepress_addons_youtube_api_const_ConstantsTest extends TubePressUnitTest
{
    public function testConstants()
    {
        $toTest = array(

            'tubepress_addons_youtube_api_const_options_names_GallerySource'        => array('youtubeMostSharedValue', 'youtubeTrendingValue', 'youtubeRelatedValue', 'favoritesValue', 'youtubeMostPopularValue', 'playlistValue', 'tagValue', 'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'most_discussedValue', 'youtubeResponsesValue', 'most_recentValue', 'most_respondedValue', 'recently_featuredValue'),
            'tubepress_addons_youtube_api_const_options_values_GallerySourceValue'    => array('favorites', 'most_discussed', 'most_recent', 'most_responded', 'youtubeMostPopular', 'playlist', 'recently_featured', 'tag', 'top_rated', 'user', 'youtubeTopFavorites', 'youtubeMostShared', 'youtubeRelated', 'youtubeTrending', 'youtubeResponses'),
            'tubepress_addons_youtube_api_const_options_values_YouTube'            => array('moderate', 'none', 'strict', 'dark', 'light', 'fadeNone', 'fadeBoth', 'fadeOnlyProgressBar', 'hide', 'showImmediate', 'showDelayed', 'all_time', 'this_month', 'this_week', 'today'),

        );

        foreach ($toTest as $className => $expected) {

            $this->assertClassHasConstants($className, $expected);
        }
    }

    private function assertArrayEquality($expected, $actual)
    {
        foreach ($expected as $expectedName) {

            $this->assertTrue(in_array($expectedName, $actual), "Missing expected array value: $expectedName");
        }

        foreach ($actual as $actualName) {

            $this->assertTrue(in_array($actualName, $expected), "Extra array value: $actualName");
        }
    }

    private function assertClassHasConstants($className, array $expected)
    {
        $actual = tubepress_impl_util_LangUtils::getDefinedConstants($className);

        $this->assertArrayEquality($expected, $actual);
    }

}
