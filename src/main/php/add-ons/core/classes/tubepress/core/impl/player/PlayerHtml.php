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
class tubepress_core_impl_player_PlayerHtml implements tubepress_core_api_player_PlayerHtmlInterface
{
    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_core_api_options_ContextInterface $context,
                                tubepress_core_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param tubepress_core_api_video_Video $vid The video to display in the player.
     *
     * @return string|null The HTML for this player with the given video, or null if not found.
     */
    public function getHtml(tubepress_core_api_video_Video $vid)
    {
        $requestedPlayerLocation   = $this->_context->get(tubepress_core_api_const_options_Names::PLAYER_LOCATION);
        $choosePlayerLocationEvent = $this->_eventDispatcher->newEventInstance($vid, array(

            'selected'                => null,
            'requestedPlayerLocation' => $requestedPlayerLocation
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION,
            $choosePlayerLocationEvent
        );

        $playerLocation = $choosePlayerLocationEvent->getArgument('selected');

        if ($playerLocation === null || (!$playerLocation instanceof tubepress_core_api_player_PlayerLocationInterface)) {

            return null;
        }

        /**
         * @var $playerLocation tubepress_core_api_player_PlayerLocationInterface
         */
        $template = $playerLocation->getTemplate();
        /*
         * Run filters for the player template construction.
         */
        $playerTemplateEvent = $this->_eventDispatcher->newEventInstance(

            $template, array(

                'video'      => $vid,
                'playerName' => $playerLocation->getName())
        );

        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION,
            $playerTemplateEvent
        );

        /*
         * Run filters for the player HTML construction.
         */
        $html = $playerTemplateEvent->getSubject()->toString();

        $playerHtmlEvent = $this->_eventDispatcher->newEventInstance($html, array(

            'video'        => $vid,
            'playerName'   => $playerLocation->getName()
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_core_api_const_event_EventNames::HTML_PLAYERLOCATION,
            $playerHtmlEvent
        );

        return $playerHtmlEvent->getSubject();
    }
}