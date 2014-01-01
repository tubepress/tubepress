<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_addons_youtube_api_const_ConstantsTest extends tubepress_test_TubePressUnitTest
{
    public function testConstants()
    {
        $toTest = array(

            'tubepress_addons_youtube_api_const_options_names_GallerySource'        => array('youtubeRelatedValue', 'favoritesValue', 'playlistValue', 'tagValue', 'userValue', 'youtubeMostPopularValue'),
            'tubepress_addons_youtube_api_const_options_values_GallerySourceValue'    => array('favorites', 'youtubeMostPopular', 'playlist', 'tag', 'user', 'youtubeRelated'),
            'tubepress_addons_youtube_api_const_options_values_YouTube'            => array('moderate', 'none', 'strict', 'dark', 'light', 'fadeNone', 'fadeBoth', 'fadeOnlyProgressBar', 'hide', 'showImmediate', 'showDelayed', 'all_time', 'today'),

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
