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
        $this->setComponentSpec(org_tubepress_ioc_IocService::MESSAGE,
            $this->newComponentSpec('org_tubepress_message_WordPressMessageService', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::SHORTCODE,
            $this->newComponentSpec('org_tubepress_shortcode_SimpleShortCodeService', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::REFERENCE,
            $this->newComponentSpec('org_tubepress_options_reference_SimpleOptionsReference', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::FEED_INSP,
            $this->newComponentSpec('org_tubepress_gdata_inspection_SimpleFeedInspectionService', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::CACHE,
            $this->newComponentSpec('org_tubepress_cache_SimpleCacheService', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::VID_FACT,
            $this->newComponentSpec('org_tubepress_video_factory_SimpleVideoFactory', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::QUERY_STR,
            $this->newComponentSpec('org_tubepress_querystring_SimpleQueryStringService', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::EMBED,
            $this->newComponentSpec('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService', array(), array(), true));
        $this->setComponentSpec(org_tubepress_ioc_IocService::PLAYER_FACT,
            $this->newComponentSpec('org_tubepress_player_factory_SimplePlayerFactory', array(), array(), true));

        /* These guys have 1 setter */
        $this->setComponentSpec(org_tubepress_ioc_IocService::VALIDATION,
            $this->newComponentSpec('org_tubepress_options_validation_SimpleInputValidationService', array(),
                array('messageService' => $this->referenceFor(org_tubepress_ioc_IocService::MESSAGE)), true
            )
        );
        $this->setComponentSpec(org_tubepress_ioc_IocService::FEED_RET,
            $this->newComponentSpec('org_tubepress_gdata_retrieval_HTTPRequest2', array(),
                array( 'cacheService' => $this->referenceFor(org_tubepress_ioc_IocService::CACHE)), true
            )
        );
        $this->setComponentSpec(org_tubepress_ioc_IocService::URL_BUILDER,
            $this->newComponentSpec('org_tubepress_url_SimpleUrlBuilder', array(),
                array('optionsManager' => $this->referenceFor(org_tubepress_ioc_IocService::OPTIONS_MGR)), true
            )
        );

        /* this guy has 2 setters */
        $this->setComponentSpec(org_tubepress_ioc_IocService::STORAGE,
            $this->newComponentSpec('org_tubepress_options_storage_WordPressStorageManager', array(),
                array(
                    'optionsReference'  => $this->referenceFor(org_tubepress_ioc_IocService::REFERENCE),
                    'validationService' => $this->referenceFor(org_tubepress_ioc_IocService::VALIDATION)
                ), true
            )
        );
        $this->setComponentSpec(org_tubepress_ioc_IocService::THUMB,
            $this->newComponentSpec('org_tubepress_thumbnail_SimpleThumbnailService', array(),
                array(
                    'optionsManager' => $this->referenceFor(org_tubepress_ioc_IocService::OPTIONS_MGR),
                    'messageService' => $this->referenceFor(org_tubepress_ioc_IocService::MESSAGE)
                ), true
            )
        );

        /* these guys have 3 setters */
        $this->setComponentSpec(org_tubepress_ioc_IocService::OPTIONS_MGR,
            $this->newComponentSpec('org_tubepress_options_manager_SimpleOptionsManager', array(),
                array(
                    'validationService' => $this->referenceFor(org_tubepress_ioc_IocService::VALIDATION),
                    'optionsReference'  => $this->referenceFor(org_tubepress_ioc_IocService::REFERENCE),
                    'storageManager'    => $this->referenceFor(org_tubepress_ioc_IocService::STORAGE)
                ), true
            )
        );
        $this->setComponentSpec(org_tubepress_ioc_IocService::PAGINATION,
            $this->newComponentSpec('org_tubepress_pagination_DiggStylePaginationService', array(),
                array(
                    'messageService'     => $this->referenceFor(org_tubepress_ioc_IocService::MESSAGE),
                    'queryStringService' => $this->referenceFor(org_tubepress_ioc_IocService::QUERY_STR),
                    'optionsManager'     => $this->referenceFor(org_tubepress_ioc_IocService::OPTIONS_MGR)
                ), true
            )
        );

        /* the big guy */
        $this->setComponentSpec(org_tubepress_ioc_IocService::GALLERY,
            $this->newComponentSpec('org_tubepress_gallery_Gallery', array(),
                array(
                    'feedInspectionService' => $this->referenceFor(org_tubepress_ioc_IocService::FEED_INSP),
                    'feedRetrievalService'  => $this->referenceFor(org_tubepress_ioc_IocService::FEED_RET),
                    'messageService'        => $this->referenceFor(org_tubepress_ioc_IocService::MESSAGE),
                    'optionsManager'        => $this->referenceFor(org_tubepress_ioc_IocService::OPTIONS_MGR),
                    'paginationService'     => $this->referenceFor(org_tubepress_ioc_IocService::PAGINATION),
                    'playerFactory'         => $this->referenceFor(org_tubepress_ioc_IocService::PLAYER_FACT),
                    'queryStringService'    => $this->referenceFor(org_tubepress_ioc_IocService::QUERY_STR),
                    'thumbnailService'      => $this->referenceFor(org_tubepress_ioc_IocService::THUMB),
                    'embeddedPlayerService' => $this->referenceFor(org_tubepress_ioc_IocService::EMBED),
                    'urlBuilderService'     => $this->referenceFor(org_tubepress_ioc_IocService::URL_BUILDER),
                    'videoFactory'          => $this->referenceFor(org_tubepress_ioc_IocService::VID_FACT),
                ), true
            )
        );
    }
}
?>
