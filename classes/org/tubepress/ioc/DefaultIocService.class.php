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
    'org_tubepress_ioc_IocService',
    'org_tubepress_player_Player',
    'org_tubepress_embedded_EmbeddedPlayerService',
    'org_tubepress_message_WordPressMessageService',
	'org_tubepress_shortcode_SimpleShortcodeService',
    'org_tubepress_options_reference_SimpleOptionsReference',
    'org_tubepress_video_feed_inspection_YouTubeFeedInspectionService',
	'org_tubepress_cache_SimpleCacheService',
	'org_tubepress_video_factory_YouTubeVideoFactory',
	'org_tubepress_querystring_SimpleQueryStringService',
	'org_tubepress_player_impl_YouTubePlayer',
    'org_tubepress_player_impl_NormalPlayer',
	'org_tubepress_options_validation_SimpleInputValidationService',
	'org_tubepress_video_feed_retrieval_HTTPRequest2',
	'org_tubepress_url_YouTubeUrlBuilder',
	'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
	'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
	'org_tubepress_player_impl_ModalPlayer',
	'org_tubepress_options_storage_WordPressStorageManager',
	'org_tubepress_options_manager_SimpleOptionsManager',
	'org_tubepress_pagination_DiggStylePaginationService',
	'org_tubepress_options_form_FormHandler',
	'org_tubepress_gallery_TubePressGalleryImpl',
    'org_tubepress_template_SimpleTemplate',
    'org_tubepress_log_LogImpl',
    'org_tubepress_video_feed_provider_ProviderImpl',
    'org_tubepress_browser_BrowserDetectorImpl',
    'org_tubepress_single_VideoImpl'));

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
        $this->def(org_tubepress_ioc_IocService::FEED_INSPECTION_SERVICE, 
            $this->impl('org_tubepress_video_feed_inspection_YouTubeFeedInspectionService'));
        $this->def(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE, 
            $this->impl('org_tubepress_querystring_SimpleQueryStringService'));
        $this->def(org_tubepress_ioc_IocService::LOG, 
            $this->impl('org_tubepress_log_LogImpl'));
        $this->def(org_tubepress_ioc_IocService::BROWSER_DETECTOR,
            $this->impl('org_tubepress_browser_BrowserDetectorImpl'));

        
        /*******************************************************************************************
         *                                      1 SETTER                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::URL_BUILDER,
            $this->impl('org_tubepress_url_YouTubeUrlBuilder', 
                array('optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER))
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . '-embedded',
            $this->impl('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
                array('optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER))
            )
        );
        $this->def(org_tubepress_ioc_IocService::GALLERY_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . "/gallery/html_templates/default.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . "/embedded_flash/youtube/html_templates/object.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::LONGTAIL_EMBEDDED_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . "/embedded_flash/longtail/html_templates/object.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . "/players/normal/html_templates/pre_gallery.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . "/players/shared/html_templates/pre_gallery_modal.tpl.php")
            )
        );
        $this->def(org_tubepress_ioc_IocService::CACHE_SERVICE,
            $this->impl('org_tubepress_cache_SimpleCacheService',
                array('log' => $this->ref(org_tubepress_ioc_IocService::LOG))
            )
        );
        $this->def(org_tubepress_ioc_IocService::OPTIONS_FORM_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . '/options_page/html_templates/options_page.tpl.php')
            )
        );
        $this->def(org_tubepress_ioc_IocService::SINGLE_VIDEO_TEMPLATE,
            $this->impl('org_tubepress_template_SimpleTemplate',
                array('path' => $uiBase . '/single_video/html_templates/default.tpl.php')
            )
        );
        $this->def(org_tubepress_player_Player::YOUTUBE . "-player",
            $this->impl('org_tubepress_player_impl_YouTubePlayer',
                array(
                    'optionsManager'  => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );

        /*******************************************************************************************
         *                                      2 SETTERS                                          *
         *******************************************************************************************/

        $this->def(org_tubepress_ioc_IocService::STORAGE_MANAGER,
            $this->impl('org_tubepress_options_storage_WordPressStorageManager', 
                array(
                    'optionsReference'  => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    'validationService' => $this->ref(org_tubepress_ioc_IocService::VALIDATION_SERVICE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE,
            $this->impl('org_tubepress_video_feed_retrieval_HTTPRequest2', 
                array(
                	'cacheService' => $this->ref(org_tubepress_ioc_IocService::CACHE_SERVICE),
                    'log' => $this->ref(org_tubepress_ioc_IocService::LOG)
                )
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL . '-embedded',
            $this->impl('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::LONGTAIL_EMBEDDED_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . '-embedded',
            $this->impl('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::SHORTCODE_SERVICE,
            $this->impl('org_tubepress_shortcode_SimpleShortcodeService',
                array(
                    'log' => $this->ref(org_tubepress_ioc_IocService::LOG),
                    'inputValidationService' => $this->ref(org_tubepress_ioc_IocService::VALIDATION_SERVICE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::VIDEO_FACTORY,
            $this->impl('org_tubepress_video_factory_YouTubeVideoFactory',
                array(
                    'log' => $this->ref(org_tubepress_ioc_IocService::LOG),
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_player_Player::NORMAL . "-player",
            $this->impl('org_tubepress_player_impl_NormalPlayer',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::STATICC . "-player",
            $this->impl('org_tubepress_player_impl_NormalPlayer',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::POPUP . "-player",
            $this->impl('org_tubepress_player_impl_ModalPlayer',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::SHADOWBOX . "-player",
            $this->impl('org_tubepress_player_impl_ModalPlayer',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE)
                )
            )
        );
        $this->def(org_tubepress_player_Player::JQMODAL . "-player",
            $this->impl('org_tubepress_player_impl_ModalPlayer',
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'template' => $this->ref(org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE)
                )
            )
        );
        
        /*******************************************************************************************
         *                                      3 SETTERS                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::OPTIONS_MANAGER,
            $this->impl('org_tubepress_options_manager_SimpleOptionsManager', 
                array(
                    'validationService' => $this->ref(org_tubepress_ioc_IocService::VALIDATION_SERVICE),
                    'optionsReference'  => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    'storageManager'    => $this->ref(org_tubepress_ioc_IocService::STORAGE_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::PAGINATION_SERVICE,
            $this->impl('org_tubepress_pagination_DiggStylePaginationService', 
                array(
                    'messageService'     => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    'queryStringService' => $this->ref(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE),
                    'optionsManager'     => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::VALIDATION_SERVICE,
            $this->impl('org_tubepress_options_validation_SimpleInputValidationService',
                array(
                    'messageService' => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    'optionsReference' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    'log' => $this->ref(org_tubepress_ioc_IocService::LOG)
                )
            )
        );
        
        /*******************************************************************************************
         *                                      4+ SETTERS                                          *
         *******************************************************************************************/
        $this->def(org_tubepress_ioc_IocService::SINGLE_VIDEO,
            $this->impl('org_tubepress_single_VideoImpl',
                array(
                    'provider'         => $this->ref(org_tubepress_ioc_IocService::VIDEO_PROVIDER),
                    'template'         => $this->ref(org_tubepress_ioc_IocService::SINGLE_VIDEO_TEMPLATE),
                    'optionsManager'   => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'optionsReference' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    'messageService'   => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    'log'              => $this->ref(org_tubepress_ioc_IocService::LOG),
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::OPTIONS_FORM_HANDLER,
            $this->impl('org_tubepress_options_form_FormHandler',
                array(
                    'messageService'   => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    'optionsReference' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    'storageManager'   => $this->ref(org_tubepress_ioc_IocService::STORAGE_MANAGER),
                    'template'         => $this->ref(org_tubepress_ioc_IocService::OPTIONS_FORM_TEMPLATE)
                )
            )
        );
        
        $this->def(org_tubepress_ioc_IocService::VIDEO_PROVIDER,
            $this->impl('org_tubepress_video_feed_provider_ProviderImpl',
                array(
                    'feedInspectionService' => $this->ref(org_tubepress_ioc_IocService::FEED_INSPECTION_SERVICE),
                    'feedRetrievalService' => $this->ref(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE),
                    'log'                   => $this->ref(org_tubepress_ioc_IocService::LOG),
                    'optionsManager'        => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'queryStringService'    => $this->ref(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE),
                    'urlBuilder'            => $this->ref(org_tubepress_ioc_IocService::URL_BUILDER),
                    'videoFactory'          => $this->ref(org_tubepress_ioc_IocService::VIDEO_FACTORY)
                )
            )
        );
        
        $this->def(org_tubepress_ioc_IocService::GALLERY,
            $this->impl('org_tubepress_gallery_TubePressGalleryImpl',
                array(
                    'messageService'        => $this->ref(org_tubepress_ioc_IocService::MESSAGE_SERVICE),
                    'optionsManager'        => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MANAGER),
                    'paginationService'     => $this->ref(org_tubepress_ioc_IocService::PAGINATION_SERVICE),
                    'queryStringService'    => $this->ref(org_tubepress_ioc_IocService::QUERY_STRING_SERVICE),
                    'videoProvider'         => $this->ref(org_tubepress_ioc_IocService::VIDEO_PROVIDER),
                    'log'                   => $this->ref(org_tubepress_ioc_IocService::LOG),
                    'optionsReference'      => $this->ref(org_tubepress_ioc_IocService::OPTIONS_REFERENCE),
                    'template'              => $this->ref(org_tubepress_ioc_IocService::GALLERY_TEMPLATE),
                    'browserDetector'       => $this->ref(org_tubepress_ioc_IocService::BROWSER_DETECTOR)
                )                
            )        
        );
    }
}
