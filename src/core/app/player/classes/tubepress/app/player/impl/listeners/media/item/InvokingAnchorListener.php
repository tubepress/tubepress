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
 * This listener is responsible for setting three media item attributes:
 *
 * tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_URL
 * tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_REL
 * tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_TARGET
 */
class tubepress_app_player_impl_listeners_media_item_InvokingAnchorListener
{
    /**
     * @var tubepress_app_player_api_PlayerHtmlInterface
     */
    private $_playerHtml;

    private static $_ATTRIBUTE_MAP = array(
        'href'   => tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_URL,
        'rel'    => tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_REL,
        'target' => tubepress_app_media_item_api_Constants::ATTRIBUTE_INVOKING_ANCHOR_TARGET,
    );

    public function __construct(tubepress_app_player_api_PlayerHtmlInterface $playerHtml)
    {
        $this->_playerHtml = $playerHtml;
    }

    public function onNewMediaItem(tubepress_lib_event_api_EventInterface $event)
    {
        $item   = $event->getSubject();
        $player = $this->_playerHtml->getActivePlayerLocation();
        $data   = $player->getInvocationAnchorAttributeArray($item);

        foreach (self::$_ATTRIBUTE_MAP as $key => $attributeName) {

            if (isset($data[$key])) {

                $item->setAttribute($attributeName, $data[$key]);
            }
        }
    }
}
