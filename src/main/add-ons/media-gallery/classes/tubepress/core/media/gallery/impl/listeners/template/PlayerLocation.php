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
class tubepress_core_media_gallery_impl_listeners_template_PlayerLocation
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    public function __construct(tubepress_core_options_api_ContextInterface $context,
                                tubepress_core_player_api_PlayerHtmlInterface $playerHtml)
    {
        $this->_context    = $context;
        $this->_playerHtml = $playerHtml;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $playerName     = $this->_context->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);
        $providerResult = $event->getArgument('page');
        $videos         = $providerResult->getItems();
        $template       = $event->getSubject();
        $galleryId      = $this->_context->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $playerHtml     = $playerHtml = $this->_showPlayerHtmlOnPageLoad($playerName) ?
            $this->_playerHtml->getHtml($videos[0], $galleryId) : '';

        $template->setVariable(tubepress_core_template_api_const_VariableNames::PLAYER_HTML, $playerHtml);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::PLAYER_NAME, $playerName);
    }

    private function _showPlayerHtmlOnPageLoad($playerName)
    {
        if ($playerName === 'normal' || $playerName === 'static') {

            return true;
        }

        return false;
    }
}