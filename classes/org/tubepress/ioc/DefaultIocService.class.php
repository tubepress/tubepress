<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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

/**
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_ioc_DefaultIocService extends org_tubepress_ioc_PhpCraftyIocService implements org_tubepress_ioc_IocService
{
    function __construct()
    {
        /* these guys have no setters that we care about */
        $this->def(org_tubepress_ioc_IocService::MESSAGE,
            $this->impl('org_tubepress_message_WordPressMessageService'));
        $this->def(org_tubepress_ioc_IocService::SHORTCODE,
            $this->impl('org_tubepress_shortcode_SimpleShortCodeService'));
        $this->def(org_tubepress_ioc_IocService::REFERENCE,
            $this->impl('org_tubepress_options_reference_SimpleOptionsReference'));
        $this->def(org_tubepress_ioc_IocService::FEED_INSP,
            $this->impl('org_tubepress_gdata_inspection_SimpleFeedInspectionService'));
        $this->def(org_tubepress_ioc_IocService::CACHE,
            $this->impl('org_tubepress_cache_SimpleCacheService'));
        $this->def(org_tubepress_ioc_IocService::VID_FACT,
            $this->impl('org_tubepress_video_factory_SimpleVideoFactory'));
        $this->def(org_tubepress_ioc_IocService::QUERY_STR,
            $this->impl('org_tubepress_querystring_SimpleQueryStringService'));
        $this->def(org_tubepress_player_Player::GREYBOX . "-player",
            $this->impl('org_tubepress_player_impl_GreyBoxPlayer'));
        $this->def(org_tubepress_player_Player::LIGHTWINDOW . "-player",
            $this->impl('org_tubepress_player_impl_LightWindowPlayer'));
        $this->def(org_tubepress_player_Player::SHADOWBOX . "-player",
            $this->impl('org_tubepress_player_impl_ShadowBoxPlayer'));
        $this->def(org_tubepress_player_Player::POPUP . "-player",
            $this->impl('org_tubepress_player_impl_PopupPlayer'));
        $this->def(org_tubepress_player_Player::YOUTUBE . "-player",
            $this->impl('org_tubepress_player_impl_YouTubePlayer'));

        /* These guys have 1 setter */
        $this->def(org_tubepress_ioc_IocService::VALIDATION,
            $this->impl('org_tubepress_options_validation_SimpleInputValidationService',
                array('messageService' => $this->ref(org_tubepress_ioc_IocService::MESSAGE))
            )
        );
        $this->def(org_tubepress_ioc_IocService::FEED_RET,
            $this->impl('org_tubepress_gdata_retrieval_HTTPRequest2', 
                array( 'cacheService' => $this->ref(org_tubepress_ioc_IocService::CACHE))
            )
        );
        $this->def(org_tubepress_ioc_IocService::URL_BUILDER,
            $this->impl('org_tubepress_url_SimpleUrlBuilder', 
                array('optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR))
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . '-embedded',
            $this->impl('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
                array('optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR))
            )
        );
        $this->def(org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL . '-embedded',
            $this->impl('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
                array('optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR))
            )
        );
        $this->def(org_tubepress_player_Player::NORMAL . "-player",
            $this->impl('org_tubepress_player_impl_NormalPlayer',
                array('optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR))
            )
        );
        
        /* this guy has 2 setters */
        $this->def(org_tubepress_ioc_IocService::STORAGE,
            $this->impl('org_tubepress_options_storage_WordPressStorageManager', 
                array(
                    'optionsReference'  => $this->ref(org_tubepress_ioc_IocService::REFERENCE),
                    'validationService' => $this->ref(org_tubepress_ioc_IocService::VALIDATION)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::THUMB,
            $this->impl('org_tubepress_thumbnail_SimpleThumbnailService', 
                array(
                    'optionsManager' => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR),
                    'messageService' => $this->ref(org_tubepress_ioc_IocService::MESSAGE)
                )
            )
        );

        /* these guys have 3 setters */
        $this->def(org_tubepress_ioc_IocService::OPTIONS_MGR,
            $this->impl('org_tubepress_options_manager_SimpleOptionsManager', 
                array(
                    'validationService' => $this->ref(org_tubepress_ioc_IocService::VALIDATION),
                    'optionsReference'  => $this->ref(org_tubepress_ioc_IocService::REFERENCE),
                    'storageManager'    => $this->ref(org_tubepress_ioc_IocService::STORAGE)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::PAGINATION,
            $this->impl('org_tubepress_pagination_DiggStylePaginationService', 
                array(
                    'messageService'     => $this->ref(org_tubepress_ioc_IocService::MESSAGE),
                    'queryStringService' => $this->ref(org_tubepress_ioc_IocService::QUERY_STR),
                    'optionsManager'     => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::W_PRINTER,
            $this->impl('org_tubepress_options_form_WidgetPrinter',
                array(
                    'messageService'   => $this->ref(org_tubepress_ioc_IocService::MESSAGE),
                    'optionsReference' => $this->ref(org_tubepress_ioc_IocService::REFERENCE),
                    'storageManager'   => $this->ref(org_tubepress_ioc_IocService::STORAGE),
                )
            )
        );

        /* 4 setters, gyea */
        $this->def(org_tubepress_ioc_IocService::FORM_HNDLER,
            $this->impl('org_tubepress_options_form_FormHandler',
                array(
                    'messageService'   => $this->ref(org_tubepress_ioc_IocService::MESSAGE),
                    'optionsReference' => $this->ref(org_tubepress_ioc_IocService::REFERENCE),
                    'storageManager'   => $this->ref(org_tubepress_ioc_IocService::STORAGE),
                    'categoryPrinter'  => $this->ref(org_tubepress_ioc_IocService::CAT_PRINTER)
                )
            )
        );
        $this->def(org_tubepress_ioc_IocService::CAT_PRINTER,
            $this->impl('org_tubepress_options_form_CategoryPrinter',
                array(
                    'messageService'   => $this->ref(org_tubepress_ioc_IocService::MESSAGE),
                    'optionsReference' => $this->ref(org_tubepress_ioc_IocService::REFERENCE),
                    'storageManager'   => $this->ref(org_tubepress_ioc_IocService::STORAGE),
                    'widgetPrinter'    => $this->ref(org_tubepress_ioc_IocService::W_PRINTER)
                )
            )
        );
        
        /* the big guy */
        $this->def(org_tubepress_ioc_IocService::GALLERY,
            $this->impl('org_tubepress_gallery_Gallery', 
                array(
                    'feedInspectionService' => $this->ref(org_tubepress_ioc_IocService::FEED_INSP),
                    'feedRetrievalService'  => $this->ref(org_tubepress_ioc_IocService::FEED_RET),
                    'messageService'        => $this->ref(org_tubepress_ioc_IocService::MESSAGE),
                    'optionsManager'        => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR),
                    'paginationService'     => $this->ref(org_tubepress_ioc_IocService::PAGINATION),
                    'queryStringService'    => $this->ref(org_tubepress_ioc_IocService::QUERY_STR),
                    'thumbnailService'      => $this->ref(org_tubepress_ioc_IocService::THUMB),
                    'urlBuilderService'     => $this->ref(org_tubepress_ioc_IocService::URL_BUILDER),
                    'videoFactory'          => $this->ref(org_tubepress_ioc_IocService::VID_FACT),
                )
            )
        );
        /* the other big guy */
        $this->def(org_tubepress_ioc_IocService::WIDGET_GALL,
            $this->impl('org_tubepress_gallery_WidgetGallery', 
                array(
                    'feedInspectionService' => $this->ref(org_tubepress_ioc_IocService::FEED_INSP),
                    'feedRetrievalService'  => $this->ref(org_tubepress_ioc_IocService::FEED_RET),
                    'messageService'        => $this->ref(org_tubepress_ioc_IocService::MESSAGE),
                    'optionsManager'        => $this->ref(org_tubepress_ioc_IocService::OPTIONS_MGR),
                    'paginationService'     => $this->ref(org_tubepress_ioc_IocService::PAGINATION),
                    'queryStringService'    => $this->ref(org_tubepress_ioc_IocService::QUERY_STR),
                    'thumbnailService'      => $this->ref(org_tubepress_ioc_IocService::THUMB),
                    'urlBuilderService'     => $this->ref(org_tubepress_ioc_IocService::URL_BUILDER),
                    'videoFactory'          => $this->ref(org_tubepress_ioc_IocService::VID_FACT),
                )
            )
        );
    }
}
?>