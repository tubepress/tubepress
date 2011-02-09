<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/values/GalleryContentModeValue.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_values_GalleryContentModeValueTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('favoritesValue', 'playlistValue', 'most_viewedValue', 'tagValue', 'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'vimeoUploadedByValue', 'vimeoLikesValue', 'vimeoAppearsInValue', 'vimeoSearchValue', 'vimeoCreditedToValue', 'vimeoChannelValue','vimeoAlbumValue', 'vimeoGroupValue');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_values_GalleryContentModeValue', $expected);
    }
}
?>
