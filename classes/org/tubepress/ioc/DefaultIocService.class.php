<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_ioc_PhpCraftyIocService',
    'org_tubepress_browser_BrowserDetectorImpl',
    'org_tubepress_cache_SimpleCacheService',
    'org_tubepress_embedded_impl_DefaultEmbeddedPlayerService',
    'org_tubepress_embedded_EmbeddedPlayerService',    
    'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
    'org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',
    'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
    'org_tubepress_gallery_TubePressGalleryImpl',
    'org_tubepress_ioc_IocService',
    'org_tubepress_ioc_Setters',
    'org_tubepress_log_LogImpl',
    'org_tubepress_message_WordPressMessageService',
    'org_tubepress_options_form_FormHandler',
    'org_tubepress_options_manager_SimpleOptionsManager',
    'org_tubepress_options_reference_SimpleOptionsReference',
    'org_tubepress_options_storage_WordPressStorageManager',
    'org_tubepress_options_validation_SimpleInputValidationService',
    'org_tubepress_pagination_DiggStylePaginationService',
    'org_tubepress_player_impl_ModalPlayer',
    'org_tubepress_player_impl_NormalPlayer',
    'org_tubepress_player_impl_YouTubePlayer',
    'org_tubepress_player_Player',
    'org_tubepress_querystring_SimpleQueryStringService',
    'org_tubepress_shortcode_SimpleShortcodeService',
    'org_tubepress_single_VideoImpl',
    'org_tubepress_template_SimpleTemplate',
    'org_tubepress_url_DelegatingUrlBuilder',
    'org_tubepress_url_VimeoUrlBuilder',
    'org_tubepress_url_YouTubeUrlBuilder',
    'org_tubepress_video_factory_DelegatingVideoFactory',
    'org_tubepress_video_factory_VimeoVideoFactory',
    'org_tubepress_video_factory_YouTubeVideoFactory',
    'org_tubepress_video_feed_inspection_DelegatingFeedInspectionService',
    'org_tubepress_video_feed_inspection_VimeoFeedInspectionService',
    'org_tubepress_video_feed_inspection_YouTubeFeedInspectionService',
    'org_tubepress_video_feed_provider_ProviderImpl',
    'org_tubepress_video_feed_retrieval_HTTPRequest2'));

/**
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_ioc_DefaultIocService extends org_tubepress_ioc_PhpCraftyIocService implements org_tubepress_ioc_IocService
{
    function __construct()
    {
        $uiBase = dirname(__FILE__) . "/../../../../ui";
        
        /*******************************************************************************************
         *                                      0 SETTERS                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::MESSAGE_SERVICE, 
            $this->impl('org_tubepress_message_WordPressMessageService'));
        $this->def(org_tubepress_ioc_IocService::OPTIONS_REFERENCE, 
            $this->impl('org_tubepress_options_reference_SimpleOptionsReference'));
        $this->def(org_tubepress_ioc_IocService::YOUTUBE_FEED_INSPECTION, 
            $this->impl('org_tubepress_video_feed_inspection_YouTubeFeedInspectionService'));
        $this->def(org_tubepress_ioc_IocService::VIMEO_FEED_INSPECTION, 
            $this->impl('org_tubepress_video_feed_inspection_VimeoFeedInspectionService'));
        $this->def(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE, 
            $this->impl('org_tubepress_querystring_SimpleQueryStringService'));
        $this->def(org_tubepress_ioc_IocService::LOG, 
            $this->impl('org_tubepress_log_LogImpl'));
        $this->def(org_tubepress_ioc_IocService::BROWSER_DETECTOR,
            $this->impl('org_tubepress_browser_BrowserDetectorImpl'));

        
        /*******************************************************************************************
         *                                      1 SETTER                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::YOUTUBE_URL_BUILDER,
            $this->impl('org_tubepress_url_YouTubeUrlBuilder', 
                array(org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER))
            )
        );
        $this->def(org_tubepress_ioc_IocService::VIMEO_URL_BUILDER,
            $this->impl('org_tubepress_url_VimeoUrlBuilder', 
                array(org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER))
            )
        );
        $this->def(org_tubepress_ioc_IocService::GALLERY_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . "/gallery/html_templates/default.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . "/embedded_flash/youtube/html_templates/object.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::VIMEO_EMBEDDED_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . "/embedded_flash/vimeo/html_templates/object.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::LONGTAIL_EMBEDDED_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . "/embedded_flash/longtail/html_templates/object.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . "/players/normal/html_templates/pre_gallery.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . "/players/shared/html_templates/pre_gallery_modal.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::CACHE_SERVICE,
            $this->impl('org_tubepress_cache_SimpleCacheService',
                array(org_tubepress_ioc_Setters::LOG => $this->ref(org_tubepress_ioc_IocService::LOG))
            )
        );
        $this->def(org_tubepress_ioc_IocService::OPTIONS_FORM_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . '/options_page/html_templates/options_page.tpl.php')
            )
        );
        $this->def(org_tubepress_ioc_IocService::SINGLE_VIDEO_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array(org_tubepress_ioc_Setters::PATH => $uiBase . '/single_video/html_templates/default.tpl.php')
            )
        );
        $this->def(org_tubepress_player_Player::YOUTUBE . "-player",
            $this->impl('org_tubepress_player_impl_YouTubePlayer',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );

        /*******************************************************************************************
         *                                      2 SETTERS                                          *
         *******************************************************************************************/

        $this->def(org_tubepress_ioc_IocService::STORAGE_MANAGER,
            $this->impl('org_tubepress_options_storage_WordPressStorageManager', 
                array(
                    org_tubepress_ioc_Setters::OPTIONS_REFERENCE => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    org_tubepress_ioc_Setters::INPUT_VALIDATION  => $this->ref(org_tubepress_ioc_IocService::VALIDATION_SERVICE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE,
            $this->impl('org_tubepress_video_feed_retrieval_HTTPRequest2', 
                array(
                    org_tubepress_ioc_Setters::CACHE => $this->ref(org_tubepress_ioc_IocService::CACHE_SERVICE),
                    org_tubepress_ioc_Setters::LOG  => $this->ref(org_tubepress_ioc_IocService::LOG)
                )
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL . '-embedded',
            $this->impl('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::LONGTAIL_EMBEDDED_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_PLAYER,
            $this->impl('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::VIMEO_EMBEDDED_PLAYER,
            $this->impl('org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::VIMEO_EMBEDDED_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::SHORTCODE_SERVICE,
            $this->impl('org_tubepress_shortcode_SimpleShortcodeService',
                array(
                    org_tubepress_ioc_Setters::LOG              => $this->ref(org_tubepress_ioc_IocService::LOG),
                    org_tubepress_ioc_Setters::INPUT_VALIDATION => $this->ref(org_tubepress_ioc_IocService::VALIDATION_SERVICE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::YOUTUBE_VIDEO_FACTORY,
            $this->impl('org_tubepress_video_factory_YouTubeVideoFactory',
                array(
                    org_tubepress_ioc_Setters::LOG             => $this->ref(org_tubepress_ioc_IocService::LOG),
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::VIMEO_VIDEO_FACTORY,
            $this->impl('org_tubepress_video_factory_VimeoVideoFactory',
                array(
                    org_tubepress_ioc_Setters::LOG             => $this->ref(org_tubepress_ioc_IocService::LOG),
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_player_Player::NORMAL . "-player",
            $this->impl('org_tubepress_player_impl_NormalPlayer',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::STATICC . "-player",
            $this->impl('org_tubepress_player_impl_NormalPlayer',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::POPUP . "-player",
            $this->impl('org_tubepress_player_impl_ModalPlayer',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::SHADOWBOX . "-player",
            $this->impl('org_tubepress_player_impl_ModalPlayer',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::JQMODAL . "-player",
            $this->impl('org_tubepress_player_impl_ModalPlayer',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE        => $this->ref(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE)
                )
            )
        );
        
        /*******************************************************************************************
         *                                      3 SETTERS                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::OPTIONS_MANAGER,
            $this->impl('org_tubepress_options_manager_SimpleOptionsManager', 
                array(
                    org_tubepress_ioc_Setters::INPUT_VALIDATION  => $this->ref(org_tubepress_ioc_IocService::VALIDATION_SERVICE),
                    org_tubepress_ioc_Setters::OPTIONS_REFERENCE => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    org_tubepress_ioc_Setters::STORAGE_MANAGER   => $this->ref(org_tubepress_ioc_IocService::STORAGE_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::DDEFAULT . '-embedded',
            $this->impl('org_tubepress_embedded_impl_DefaultEmbeddedPlayerService',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::YT_EMBED        => $this->ref(org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_PLAYER),
                    org_tubepress_ioc_Setters::VIMEO_EMBED     => $this->ref(org_tubepress_ioc_IocService::VIMEO_EMBEDDED_PLAYER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::PAGINATION_SERVICE,
            $this->impl('org_tubepress_pagination_DiggStylePaginationService', 
                array(
                    org_tubepress_ioc_Setters::MESSAGE_SERVICE  => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    org_tubepress_ioc_Setters::QUERYSTRING      => $this->ref(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE),
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER  => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::VALIDATION_SERVICE,
            $this->impl('org_tubepress_options_validation_SimpleInputValidationService',
                array(
                    org_tubepress_ioc_Setters::MESSAGE_SERVICE   => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    org_tubepress_ioc_Setters::OPTIONS_REFERENCE => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    org_tubepress_ioc_Setters::LOG               => $this->ref(org_tubepress_ioc_IocService::LOG)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::URL_BUILDER,
            $this->impl('org_tubepress_url_DelegatingUrlBuilder',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER   => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::YT_URL_BUILDER    => $this->ref(org_tubepress_ioc_IocService::YOUTUBE_URL_BUILDER),
                    org_tubepress_ioc_Setters::VIMEO_URL_BUILDER => $this->ref(org_tubepress_ioc_IocService::VIMEO_URL_BUILDER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::FEED_INSPECTION_SERVICE,
            $this->impl('org_tubepress_video_feed_inspection_DelegatingFeedInspectionService',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER  => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::YT_INSPECTION    => $this->ref(org_tubepress_ioc_IocService::YOUTUBE_FEED_INSPECTION),
                    org_tubepress_ioc_Setters::VIMEO_INSPECTION => $this->ref(org_tubepress_ioc_IocService::VIMEO_FEED_INSPECTION)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::VIDEO_FACTORY,
            $this->impl('org_tubepress_video_factory_DelegatingVideoFactory',
                array(
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::YT_FACTORY      => $this->ref(org_tubepress_ioc_IocService::YOUTUBE_VIDEO_FACTORY),
                    org_tubepress_ioc_Setters::VIMEO_FACTORY   => $this->ref(org_tubepress_ioc_IocService::VIMEO_VIDEO_FACTORY)
                )
            )
        );
        
        /*******************************************************************************************
         *                                      4+ SETTERS                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::SINGLE_VIDEO,
            $this->impl('org_tubepress_single_VideoImpl',
                array(
                    org_tubepress_ioc_Setters::PROVIDER          => $this->ref(org_tubepress_ioc_IocService::VIDEO_PROVIDER),
                    org_tubepress_ioc_Setters::TEMPLATE          => $this->ref(org_tubepress_ioc_IocService::SINGLE_VIDEO_TEMPLATE),
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER   => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::OPTIONS_REFERENCE => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    org_tubepress_ioc_Setters::MESSAGE_SERVICE   => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    org_tubepress_ioc_Setters::LOG               => $this->ref(org_tubepress_ioc_IocService::LOG),
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::OPTIONS_FORM_HANDLER,
            $this->impl('org_tubepress_options_form_FormHandler',
                array(
                    org_tubepress_ioc_Setters::MESSAGE_SERVICE   => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    org_tubepress_ioc_Setters::OPTIONS_REFERENCE => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    org_tubepress_ioc_Setters::STORAGE_MANAGER   => $this->ref(org_tubepress_ioc_IocService::STORAGE_MANAGER),
                    org_tubepress_ioc_Setters::TEMPLATE          => $this->ref(org_tubepress_ioc_IocService::OPTIONS_FORM_TEMPLATE)
                )
            )
        );
        
        $this->def(org_tubepress_ioc_IocService::VIDEO_PROVIDER,
            $this->impl('org_tubepress_video_feed_provider_ProviderImpl',
                array(
                    org_tubepress_ioc_Setters::FEED_INSPECTION => $this->ref(org_tubepress_ioc_IocService::FEED_INSPECTION_SERVICE),
                    org_tubepress_ioc_Setters::FEED_RETRIEVAL  => $this->ref(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE),
                    org_tubepress_ioc_Setters::LOG             => $this->ref(org_tubepress_ioc_IocService::LOG),
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::QUERYSTRING     => $this->ref(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE),
                    org_tubepress_ioc_Setters::URL_BUILDER     => $this->ref(org_tubepress_ioc_IocService::URL_BUILDER),
                    org_tubepress_ioc_Setters::VIDEO_FACTORY   => $this->ref(org_tubepress_ioc_IocService::VIDEO_FACTORY)
                )
            )
        );
        
        $this->def(org_tubepress_ioc_IocService::GALLERY,
            $this->impl('org_tubepress_gallery_TubePressGalleryImpl',
                array(
                    org_tubepress_ioc_Setters::MESSAGE_SERVICE   => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    org_tubepress_ioc_Setters::OPTIONS_MANAGER   => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    org_tubepress_ioc_Setters::PAGINATION        => $this->ref(org_tubepress_ioc_IocService::PAGINATION_SERVICE),
                    org_tubepress_ioc_Setters::QUERYSTRING       => $this->ref(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE),
                    org_tubepress_ioc_Setters::PROVIDER          => $this->ref(org_tubepress_ioc_IocService::VIDEO_PROVIDER),
                    org_tubepress_ioc_Setters::LOG               => $this->ref(org_tubepress_ioc_IocService::LOG),
                    org_tubepress_ioc_Setters::OPTIONS_REFERENCE => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    org_tubepress_ioc_Setters::TEMPLATE          => $this->ref(org_tubepress_ioc_IocService::GALLERY_TEMPLATE),
                    org_tubepress_ioc_Setters::BROWSER_DETECTOR  => $this->ref(org_tubepress_ioc_IocService::BROWSER_DETECTOR)
                )                
            )        
        );
    }
}
