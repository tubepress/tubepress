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

class tubepress_app_player_impl_PlayerHtml implements tubepress_app_player_api_PlayerHtmlInterface
{
    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_lib_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    /**
     * @var tubepress_app_player_api_PlayerLocationInterface[]
     */
    private $_playerLocations;

    public function __construct(tubepress_app_options_api_ContextInterface          $context,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Get's the player HTML for the given media item. This HTML will be loaded
     * into the DOM on page load.
     *
     * @param tubepress_app_media_item_api_MediaItem $mediaItem The item to display in the player.
     *
     * @return string The HTML for this player with the given item. May be empty if this player doesn't need
     *                any HTML loaded on the page load.
     *
     * @api
     * @since 4.0.0
     */
    public function getStaticHtml(tubepress_app_media_item_api_MediaItem $mediaItem)
    {
        return $this->_getHtml($mediaItem, false);
    }

    /**
     * Get's the Ajax HTML for the TubePress "player"
     *
     * @param tubepress_app_media_item_api_MediaItem $mediaItem The item to display in the player.
     *
     * @return string The HTML for this player with the given item. May be empty if this player doesn't support
     *                Ajax.
     *
     * @api
     * @since 4.0.0
     */
    public function getAjaxHtml(tubepress_app_media_item_api_MediaItem $mediaItem)
    {
        return $this->_getHtml($mediaItem, true);
    }

    private function _getHtml(tubepress_app_media_item_api_MediaItem $mediaItem, $ajax)
    {
        $playerLocation = $this->getActivePlayerLocation();

        if ($ajax) {

            $templatePaths = $playerLocation->getTemplatePathsForAjaxContent();

        } else {

            $templatePaths = $playerLocation->getTemplatePathsForStaticContent();
        }

        $template = $this->_templateFactory->fromFilesystem($templatePaths);

        if (!$template) {

            return '';
        }

        /*
         * Run filters for the player template construction.
         */
        $playerTemplateEvent = $this->_eventDispatcher->newEventInstance($template, array(

            'item'           => $mediaItem,
            'playerLocation' => $playerLocation,
            'isAjax'         => $ajax,
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_app_player_api_Constants::EVENT_PLAYER_TEMPLATE,
            $playerTemplateEvent
        );

        $playerTemplate = $playerTemplateEvent->getSubject();

        /*
         * Run filters for the player HTML construction.
         */
        $html = $playerTemplate->toString();

        $playerHtmlEvent = $this->_eventDispatcher->newEventInstance($html, array(

            'item'           => $mediaItem,
            'playerLocation' => $playerLocation,
            'isAjax'         => $ajax,
        ));

        $this->_eventDispatcher->dispatch(

            tubepress_app_player_api_Constants::EVENT_PLAYER_HTML,
            $playerHtmlEvent
        );

        return $playerHtmlEvent->getSubject();
    }

    public function setPlayerLocations(array $playerLocations)
    {
        $this->_playerLocations = $playerLocations;
    }

    /**
     * @return tubepress_app_player_api_PlayerLocationInterface
     *
     * @api
     * @since 4.0.0
     */
    public function getActivePlayerLocation()
    {
        $playerLocation            = $this->_getPlayerLocationFromContextSetting();
        $choosePlayerLocationEvent = $this->_eventDispatcher->newEventInstance($playerLocation);

        $this->_eventDispatcher->dispatch(

            tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT,
            $choosePlayerLocationEvent
        );

        /**
         * @var $playerLocation tubepress_app_player_api_PlayerLocationInterface
         */
        $playerLocation = $choosePlayerLocationEvent->getSubject();

        if ($playerLocation === null || (!$playerLocation instanceof tubepress_app_player_api_PlayerLocationInterface)) {

            throw new RuntimeException('No suitable player locations found.');
        }

        return $playerLocation;
    }

    private function _getPlayerLocationFromContextSetting()
    {
        $name = $this->_context->get(tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION);

        foreach ($this->_playerLocations as $playerLocation) {

            if ($playerLocation->getName() === $name) {

                return $playerLocation;
            }
        }

        return null;
    }
}