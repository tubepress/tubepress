<?php
require_once dirname(__FILE__) . '/../../../../../../../../classes/org/tubepress/api/const/options/values/GalleryContentMode.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../ClassConstantTestUtility.php';

class org_tubepress_api_const_options_values_GalleryContentModeTest extends TubePressUnitTest {
    
    function testConstants()
    {
        $expected = array('favorites', 'recently_featured', 'mobile', 'most_discussed', 'most_recent', 'most_responded', 'playlist', 'most_viewed', 'tag', 'youtubeTopFavorites','top_rated', 'user', 'vimeoUploadedBy', 'vimeoLikes', 'vimeoAppearsIn', 'vimeoSearch', 'vimeoCreditedTo', 'vimeoChannel','vimeoAlbum', 'vimeoGroup');

        org_tubepress_api_const_ClassConstantTestUtility::performTest('org_tubepress_api_const_options_values_GalleryContentMode', $expected);
    }



}
?>
