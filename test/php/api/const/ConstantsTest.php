<?php

require_once BASE . '/test/includes/TubePressUnitTest.php';

org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Cache',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_options_names_GallerySource',
	'org_tubepress_api_const_options_names_InteractiveSearch',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_names_Thumbs',
    'org_tubepress_api_const_options_names_WordPress',
    'org_tubepress_api_const_options_values_GallerySourceValue',
    'org_tubepress_api_const_options_values_OrderByValue',
    'org_tubepress_api_const_options_values_OutputValue',
    'org_tubepress_api_const_options_values_PlayerImplementationValue',
    'org_tubepress_api_const_options_values_PlayerLocationValue',
    'org_tubepress_api_const_options_values_SafeSearchValue',
    'org_tubepress_api_const_options_values_TimeFrameValue',
    'org_tubepress_api_const_plugin_EventName',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_const_querystring_QueryParamName',
    'org_tubepress_api_const_template_Variable'
));

class org_tubepress_api_const_ConstantsTest extends TubePressUnitTest {

    function testConstants()
    {
        $toTest = array(

            'org_tubepress_api_const_options_names_Advanced'                    => array('debugging_enabled', 'disableHttpTransportCurl', 'disableHttpTransportExtHttp', 'disableHttpTransportFopen', 'disableHttpTransportFsockOpen', 'disableHttpTransportStreams', 'keyword', 'galleryId'),
        	'org_tubepress_api_const_options_names_Cache'                       => array('cacheEnabled', 'cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds'),
            'org_tubepress_api_const_options_names_Embedded' 					=> array('playerImplementation', 'embeddedHeight', 'embeddedWidth', 'lazyPlay', 'autoplay', 'fullscreen', 'hd', 'loop', 'playerColor', 'playerHighlight', 'showInfo', 'showRelated', 'playerLocation'),
            'org_tubepress_api_const_options_names_Feed'						=> array('orderBy', 'videoBlacklist', 'developerKey', 'embeddableOnly', 'filter_racy', 'resultCountCap', 'searchResultsRestrictedToUser', 'vimeoKey', 'vimeoSecret'),
            'org_tubepress_api_const_options_names_GallerySource'				=> array('favoritesValue', 'most_viewedValue', 'playlistValue', 'tagValue', 'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'vimeoAlbumValue', 'vimeoAppearsInValue', 'vimeoChannelValue', 'vimeoCreditedToValue', 'vimeoGroupValue', 'vimeoLikesValue', 'vimeoSearchValue', 'vimeoUploadedByValue', 'most_discussedValue', 'most_recentValue', 'most_respondedValue', 'recently_featured'),
            'org_tubepress_api_const_options_names_InteractiveSearch'			=> array('searchProvider', 'searchResultsDomId', 'searchResultsOnly', 'searchResultsUrl'),
            'org_tubepress_api_const_options_names_Meta'						=> array('author', 'category', 'description', 'id', 'length', 'likes', 'rating', 'ratings', 'tags', 'title', 'uploaded', 'url', 'views', 'dateFormat', 'descriptionLimit', 'relativeDates'),
            'org_tubepress_api_const_options_names_Output'						=> array('mode', 'output', 'video'),
            'org_tubepress_api_const_options_names_Thumbs'						=> array('theme', 'ajaxPagination', 'resultsPerPage', 'hqThumbs', 'thumbHeight', 'thumbWidth', 'fluidThumbs', 'paginationAbove', 'paginationBelow', 'randomize_thumbnails'),
            'org_tubepress_api_const_options_names_WordPress'			        => array('widget-tagstring', 'widget-title', 'showVimeoOptions', 'showYouTubeOptions'),
            'org_tubepress_api_const_options_values_GallerySourceValue'		    => array('favorites', 'most_discussed', 'most_recent', 'most_responded', 'most_viewed', 'playlist', 'recently_featured', 'tag', 'top_rated', 'user', 'vimeoAlbum', 'vimeoAppearsIn', 'vimeoChannel', 'vimeoCreditedTo', 'vimeoGroup', 'vimeoLikes', 'vimeoSearch', 'vimeoUploadedBy', 'youtubeTopFavorites'),
            'org_tubepress_api_const_options_values_OrderByValue'				=> array('commentCount', 'duration', 'newest', 'oldest', 'position', 'published', 'random', 'rating', 'relevance', 'title', 'viewCount'),
            'org_tubepress_api_const_options_values_OutputValue'				=> array('ajaxSearchInput', 'searchInput', 'searchResults', 'player'),
            'org_tubepress_api_const_options_values_PlayerImplementationValue'	=> array('embedplus', 'longtail', 'provider_based'),
            'org_tubepress_api_const_options_values_PlayerLocationValue'		=> array('fancybox', 'jqmodal', 'normal', 'popup', 'shadowbox', 'solo', 'static', 'tinybox', 'vimeo', 'youtube', 'detached'),
            'org_tubepress_api_const_options_values_SafeSearchValue'			=> array('moderate', 'none', 'strict'),
            'org_tubepress_api_const_options_values_TimeFrameValue'				=> array('all_time', 'this_month', 'this_week', 'today'),
            'org_tubepress_api_const_plugin_EventName'							=> array('boot'),
            'org_tubepress_api_const_plugin_FilterPoint'						=> array('embeddedHtml', 'embeddedTemplate', 'galleryHtml', 'galleryTemplate', 'html', 'paginationHtml', 'playerHtml', 'playerTemplate', 'providerResult', 'searchInputHtml', 'searchInputTemplate', 'singleVideoHtml', 'singleVideoTemplate', 'video'),
            'org_tubepress_api_const_querystring_QueryParamName'				=> array('tubepress_debug', 'tubepress_page', 'tubepress_search', 'tubepress_shortcode', 'tubepress_video'),
            'org_tubepress_api_const_template_Variable'                         => array('autostart', 'bottomPagination', 'embeddedColorHightlight', 'embeddedColorPrimary', 'embeddedFullscreen', 'embeddedHeight', 'embeddedImplementationName', 'embeddedSource', 'embeddedWidth', 'galleryId', 'homeURL', 'metaLabels', 'playerName', 'preGallery', 'searchButton', 'searchHandlerUrl', 'searchHiddenInputs', 'searchTargetDomId', 'searchTerms', 'shouldShow', 'thumbHeight', 'thumbWidth', 'topPagination', 'tubepressBaseUrl', 'video', 'videoArray', 'videoId')

        );

        foreach ($toTest as $className => $expected) {
            TubePressUnitTest::assertClassHasConstants($className, $expected);
        }
    }
}
