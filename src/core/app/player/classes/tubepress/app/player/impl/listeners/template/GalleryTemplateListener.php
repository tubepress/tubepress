<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_app_player_impl_listeners_template_GalleryTemplateListener
{
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    public function __construct(tubepress_app_options_api_ContextInterface   $context,
                                tubepress_app_player_api_PlayerHtmlInterface $playerHtml)
    {
        $this->_context    = $context;
        $this->_playerHtml = $playerHtml;
    }

    public function onGalleryTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        $player = $this->_playerHtml->getActivePlayerLocation();

        /**
         * @var $template tubepress_lib_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        /**
         * @var $page tubepress_app_media_provider_api_Page
         */
        $page = $event->getArgument('page');

        $mediaItems = $page->getItems();
        $galleryId  = $this->_context->get(tubepress_app_html_api_Constants::OPTION_GALLERY_ID);
        $playerHtml = '';

        if ($player && !empty($mediaItems)) {

            $playerHtml = $this->_playerHtml->getStaticHtml($mediaItems[0], $galleryId);
        }

        $template->setVariable(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_PLAYER_HTML, $playerHtml);
        $template->setVariable(tubepress_app_player_api_Constants::TEMPLATE_VAR_NAME, $player->getName());
    }
}
