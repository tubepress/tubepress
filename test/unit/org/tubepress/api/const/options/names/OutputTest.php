<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/names/Output.class.php';

class org_tubepress_api_const_options_names_OutputTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array('searchResultsDomId', 'mode', 'video', 'output', 'favoritesValue', 'playlistValue', 'most_viewedValue', 'tagValue', 'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'vimeoUploadedByValue', 'vimeoLikesValue', 'vimeoAppearsInValue', 'vimeoSearchValue', 'vimeoCreditedToValue', 'vimeoChannelValue','vimeoAlbumValue', 'vimeoGroupValue', 'searchResultsUrl', 'searchResultsOnly', 'searchProvider');

        $actual = TubePressConstantsTestUtils::getConstantsForClass('org_tubepress_api_const_options_names_Output');
        
        TubePressArrayTestUtils::checkArrayEquality($expected, $actual);
        
    }
}