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
 * Sets some base parameters to send to TubePressGallery.init().
 */
class tubepress_core_impl_listeners_html_cssjs_GalleryInitJsBaseParams
{
    private static $_PROPERTY_NVPMAP = 'nvpMap';

    private static $_PROPERTY_JSMAP = 'jsMap';

    private static $_NAME_PARAM_PLAYERJSURL          = 'playerLocationJsUrl';
    private static $_NAME_PARAM_PLAYER_PRODUCES_HTML = 'playerLocationProducesHtml';

    /**
     * @var array
     */
    private $_playerLocations = array();

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_options_ProviderInterface
     */
    private $_optionProvider;

    /**
     * @var tubepress_core_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_core_api_options_ContextInterface         $context,
                                tubepress_core_api_options_ProviderInterface        $optionProvider,
                                tubepress_core_api_environment_EnvironmentInterface $environment)
    {
        $this->_context        = $context;
        $this->_optionProvider = $optionProvider;
        $this->_environment    = $environment;
    }

    /**
     * The following options are required by JS, so we explicity set them:
     *
     *  ajaxPagination
     *  autoNext
     *  embeddedHeight
     *  embeddedWidth
     *  fluidThumbs
     *  httpMethod
     *  playerLocation
     *
     * The following options are JS-specific
     *
     *  playerJsUrl
     *  playerLocationProducesHtml
     *
     * Otherwise, we simply set any "custom" options so they can be passed back in via Ajax operations.
     */
    public function onGalleryInitJs(tubepress_core_api_event_EventInterface $event)
    {
        $args = $event->getSubject();

        $requiredNvpMap = $this->_buildRequiredNvpMap();
        $jsMap          = $this->_buildJsMap();
        $customNvpMap   = $this->_context->getAllInMemory();

        $nvpMap = array_merge($requiredNvpMap, $customNvpMap);

        $newArgs = array(

            self::$_PROPERTY_NVPMAP => $this->_convertBooleans($nvpMap),
            self::$_PROPERTY_JSMAP  => $this->_convertBooleans($jsMap),
        );

        $event->setSubject(array_merge($args, $newArgs));
    }

    public function setPluggablePlayerLocations(array $playerLocations)
    {
        $this->_playerLocations = $playerLocations;
    }

    private function _buildJsMap()
    {
        $toReturn = array();

        $playerLocation = $this->_findPlayerLocation();

        if ($playerLocation !== null) {

            $toReturn[self::$_NAME_PARAM_PLAYERJSURL]          = $this->_getPlayerJsUrl($playerLocation);
            $toReturn[self::$_NAME_PARAM_PLAYER_PRODUCES_HTML] = (bool) $playerLocation->producesHtml();
        }

        $requiredOptions = array(

            tubepress_core_api_const_options_Names::AJAX_PAGINATION,
            tubepress_core_api_const_options_Names::FLUID_THUMBS,
            tubepress_core_api_const_options_Names::HTTP_METHOD,
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->_context->get($optionName);
        }

        return $toReturn;
    }

    private function _buildRequiredNvpMap()
    {
        $toReturn = array();

        $requiredOptions = array(

            tubepress_core_api_const_options_Names::EMBEDDED_HEIGHT,
            tubepress_core_api_const_options_Names::EMBEDDED_WIDTH,
            tubepress_core_api_const_options_Names::PLAYER_LOCATION
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->_context->get($optionName);
        }

        return $toReturn;
    }

    private function _findPlayerLocation()
    {
        $requestedPlayerName = $this->_context->get(tubepress_core_api_const_options_Names::PLAYER_LOCATION);

        /**
         * @var $playerLocation tubepress_core_api_player_PlayerLocationInterface
         */
        foreach ($this->_playerLocations as $playerLocation) {

            if ($playerLocation->getName() === $requestedPlayerName) {

                return $playerLocation;
            }
        }

        return null;
    }

    private function _getPlayerJsUrl(tubepress_core_api_player_PlayerLocationInterface $player)
    {
        return rtrim($player->getPlayerJsUrl($this->_environment), '/');
    }

    private function _convertBooleans($map)
    {
        foreach ($map as $key => $value) {

            if (!$this->_optionProvider->hasOption($key) || !$this->_optionProvider->isBoolean($key)) {

                continue;
            }

            $map[$key] = $value ? true : false;
        }

        return $map;
    }
}