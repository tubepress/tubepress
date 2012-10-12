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
class tubepress_api_const_ConstantsTest extends TubePressUnitTest
{
    function testConstants()
    {
        $toTest = array(

            'tubepress_api_const_options_names_Advanced'                    => array('debugging_enabled', 'keyword', 'galleryId', 'https'),
            'tubepress_api_const_options_names_Cache'                       => array('cacheEnabled', 'cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds'),
            'tubepress_api_const_options_names_Embedded' 					=> array('playerImplementation', 'embeddedHeight', 'embeddedWidth', 'lazyPlay', 'autoplay', 'loop', 'showInfo', 'playerLocation', 'autoNext', 'sequence', 'enableJsApi'),
            'tubepress_api_const_options_names_Feed'						=> array('orderBy', 'videoBlacklist', 'resultCountCap', 'searchResultsRestrictedToUser',  'perPageSort'),
            'tubepress_api_const_options_names_InteractiveSearch'			=> array('searchProvider', 'searchResultsDomId', 'searchResultsOnly', 'searchResultsUrl'),
            'tubepress_api_const_options_names_Meta'						=> array('author', 'category', 'description', 'id', 'length', 'rating', 'ratings', 'likes', 'rating', 'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'dateFormat', 'descriptionLimit', 'relativeDates'),
            'tubepress_api_const_options_names_OptionsUi'					=> array('optionsUiProvidersToHide'),
            'tubepress_api_const_options_names_Output'						=> array('mode', 'output', 'video'),
            'tubepress_api_const_options_names_Thumbs'						=> array('theme', 'ajaxPagination', 'resultsPerPage', 'hqThumbs', 'thumbHeight', 'thumbWidth', 'fluidThumbs', 'paginationAbove', 'paginationBelow', 'randomize_thumbnails'),
            'tubepress_api_const_options_values_OrderByValue'				=> array('commentCount', 'duration', 'newest', 'oldest', 'position', 'random', 'rating', 'relevance', 'title', 'viewCount'),
            'tubepress_api_const_options_values_PerPageSortValue'           => array('commentCount', 'duration', 'newest', 'oldest', 'random', 'rating', 'none', 'title', 'viewCount'),
            'tubepress_api_const_options_values_PlayerImplementationValue'	=> array('provider_based'),
            'tubepress_api_const_template_Variable'                         => array('autostart', 'bottomPagination', 'embeddedColorHightlight', 'embeddedColorPrimary', 'embeddedFullscreen', 'embeddedHeight', 'embeddedImplementationName', 'embeddedSource', 'embeddedWidth', 'galleryId', 'homeURL', 'metaLabels', 'playerName', 'preGallery', 'searchButton', 'searchHandlerUrl', 'searchHiddenInputs', 'searchTargetDomId', 'searchTerms', 'shouldShow', 'thumbHeight', 'thumbWidth', 'topPagination', 'tubepressBaseUrl', 'video', 'videoArray', 'videoId', 'shortcode')

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
