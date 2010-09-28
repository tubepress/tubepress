<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Gallery.class.php';
require_once 'AbstractOptionsCategoryTest.php';

class org_tubepress_options_category_GalleryTest extends org_tubepress_options_category_AbstractOptionsCategoryTest {
    
    protected function getClassName()
    {
        return 'org_tubepress_options_category_Gallery';
    }
    
    protected function getExpectedNames()
    {
        return array('mode', 'favoritesValue', 'most_viewedValue', 'playlistValue', 
            'tagValue', 'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'video', 'vimeoUploadedByValue',
            'vimeoLikesValue', 'vimeoAppearsInValue', 'vimeoSearchValue', 'vimeoCreditedToValue',
            'vimeoChannelValue', 'vimeoAlbumValue', 'vimeoGroupValue');
    }
}
?>