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
 * Handles applying the player HTML to the gallery template.
 */
class tubepress_core_impl_listeners_template_ThumbGalleryPlayerLocation
{
    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_player_PlayerHtmlInterface
     */
    private $_playerHtml;

    public function __construct(tubepress_core_api_options_ContextInterface $context,
                                tubepress_core_api_player_PlayerHtmlInterface $playerHtml)
    {
        $this->_context    = $context;
        $this->_playerHtml = $playerHtml;
    }

    public function onGalleryTemplate(tubepress_core_api_event_EventInterface $event)
    {
        $playerName     = $this->_context->get(tubepress_core_api_const_options_Names::PLAYER_LOCATION);
        $providerResult = $event->getArgument('videoGalleryPage');
        $videos         = $providerResult->getVideos();
        $template       = $event->getSubject();
        $galleryId      = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_ID);
        $playerHtml     = $playerHtml = $this->_showPlayerHtmlOnPageLoad($playerName) ?
            $this->_playerHtml->getHtml($videos[0], $galleryId) : '';

        $template->setVariable(tubepress_core_api_const_template_Variable::PLAYER_HTML, $playerHtml);
        $template->setVariable(tubepress_core_api_const_template_Variable::PLAYER_NAME, $playerName);
    }

    private function _showPlayerHtmlOnPageLoad($playerName)
    {
        if ($playerName === 'normal' || $playerName === 'static') {

            return true;
        }

        return false;
    }
}