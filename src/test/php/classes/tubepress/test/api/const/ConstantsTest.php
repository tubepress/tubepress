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
class tubepress_test_api_const_ConstantsTest extends tubepress_test_TubePressUnitTest
{
    public function testConstants()
    {
        $toTest = array(

            'tubepress_api_const_options_names_Advanced'                    => array('debugging_enabled', 'keyword', 'galleryId', 'https', 'httpMethod'),
            'tubepress_api_const_options_names_Cache'                       => array('cacheEnabled', 'cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds'),
            'tubepress_api_const_options_names_Embedded'                     => array('playerImplementation', 'embeddedHeight', 'embeddedWidth', 'lazyPlay', 'autoplay', 'loop', 'showInfo', 'playerLocation', 'autoNext', 'sequence', 'enableJsApi'),
            'tubepress_api_const_options_names_Feed'                        => array('orderBy', 'videoBlacklist', 'resultCountCap', 'searchResultsRestrictedToUser',  'perPageSort'),
            'tubepress_api_const_options_names_InteractiveSearch'            => array('searchProvider', 'searchResultsOnly', 'searchResultsUrl'),
            'tubepress_api_const_options_names_Meta'                        => array('author', 'category', 'description', 'id', 'length', 'rating', 'ratings', 'likes', 'rating', 'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'dateFormat', 'descriptionLimit', 'relativeDates'),
            'tubepress_api_const_options_names_OptionsUi'                    => array('disabledOptionsPageParticipants'),
            'tubepress_api_const_options_names_Output'                        => array('mode', 'output', 'video'),
            'tubepress_api_const_options_names_Thumbs'                        => array('theme', 'ajaxPagination', 'resultsPerPage', 'hqThumbs', 'thumbHeight', 'thumbWidth', 'fluidThumbs', 'paginationAbove', 'paginationBelow', 'randomize_thumbnails'),
            'tubepress_api_const_options_values_OrderByValue'                => array('default', 'commentCount', 'duration', 'newest', 'oldest', 'position', 'random', 'rating', 'relevance', 'title', 'viewCount', 'reversedPosition'),
            'tubepress_api_const_options_values_PerPageSortValue'           => array('commentCount', 'duration', 'newest', 'oldest', 'random', 'rating', 'none', 'title', 'viewCount'),
            'tubepress_api_const_options_values_PlayerImplementationValue'    => array('provider_based'),
            'tubepress_api_const_template_Variable'                         => array('videoDomId', 'videoProviderName', 'autostart', 'bottomPagination', 'embeddedColorHightlight', 'embeddedColorPrimary', 'embeddedFullscreen', 'embeddedHeight', 'embeddedImplementationName', 'embeddedSource', 'embeddedWidth', 'galleryId', 'homeURL', 'metaLabels', 'playerName', 'preGallery', 'searchButton', 'searchHandlerUrl', 'searchHiddenInputs', 'searchTargetDomId', 'searchTerms', 'shouldShow', 'thumbHeight', 'thumbWidth', 'topPagination', 'tubepressBaseUrl', 'video', 'videoArray', 'videoId')

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
