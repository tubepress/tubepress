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

abstract class tubepress_core_html_gallery_impl_listeners_AbstractGalleryListener
{
    /**
     * @var tubepress_core_player_api_PlayerLocationInterface[]
     */
    private $_playerLocations = array();

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_options_api_ReferenceInterface
     */
    private $_optionReference;

    public function __construct(tubepress_core_options_api_ContextInterface   $context,
                                tubepress_core_options_api_ReferenceInterface $optionReference)
    {
        $this->_context         = $context;
        $this->_optionReference = $optionReference;
    }

    /**
     * @param tubepress_core_player_api_PlayerLocationInterface[] $playerLocations
     */
    public function setPlayerLocations(array $playerLocations)
    {
        $this->_playerLocations = $playerLocations;
    }

    /**
     * @return tubepress_core_options_api_ContextInterface
     */
    protected function getExecutionContext()
    {
        return $this->_context;
    }

    /**
     * @return tubepress_core_options_api_ReferenceInterface
     */
    protected function getOptionReference()
    {
        return $this->_optionReference;
    }

    /**
     * @return null|tubepress_core_player_api_PlayerLocationInterface
     */
    protected function findCurrentPlayerLocation()
    {
        $requestedPlayerName = $this->_context->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);

        /**
         * @var $playerLocation tubepress_core_player_api_PlayerLocationInterface
         */
        foreach ($this->_playerLocations as $playerLocation) {

            if ($playerLocation->getName() === $requestedPlayerName) {

                return $playerLocation;
            }
        }

        return null;
    }
}