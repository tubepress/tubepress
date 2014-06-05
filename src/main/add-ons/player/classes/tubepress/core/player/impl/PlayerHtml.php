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
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
class tubepress_core_player_impl_PlayerHtml implements tubepress_core_player_api_PlayerHtmlInterface
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_core_media_item_api_MediaItem $mediaItem The video to display in the player.
     *
     * @return string|null The HTML for this player with the given video, or null if not found.
     */
    public function getHtml(tubepress_core_media_item_api_MediaItem $mediaItem)
    {
        $requestedPlayerLocation   = $this->_context->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);
        $choosePlayerLocationEvent = $this->_eventDispatcher->newEventInstance($mediaItem, array(

            'playerLocation'              => null,
            'requestedPlayerLocationName' => $requestedPlayerLocation
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT,
            $choosePlayerLocationEvent
        );

        $playerLocation = $choosePlayerLocationEvent->getArgument('playerLocation');

        if ($playerLocation === null || (!$playerLocation instanceof tubepress_core_player_api_PlayerLocationInterface)) {

            return null;
        }

        /**
         * @var $playerLocation tubepress_core_player_api_PlayerLocationInterface
         */
        $templatePaths = $playerLocation->getPathsForTemplateFactory();
        $template      = $this->_templateFactory->fromFilesystem($templatePaths);

        /*
         * Run filters for the player template construction.
         */
        $playerTemplateEvent = $this->_eventDispatcher->newEventInstance($template, array(

            'item'           => $mediaItem,
            'playerLocation' => $playerLocation
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_core_player_api_Constants::EVENT_PLAYER_TEMPLATE,
            $playerTemplateEvent
        );

        /*
         * Run filters for the player HTML construction.
         */
        $html = $playerTemplateEvent->getSubject()->toString();

        $playerHtmlEvent = $this->_eventDispatcher->newEventInstance($html, array(

            'item'           => $mediaItem,
            'playerLocation' => $playerLocation
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_core_player_api_Constants::EVENT_PLAYER_HTML,
            $playerHtmlEvent
        );

        return $playerHtmlEvent->getSubject();
    }
}