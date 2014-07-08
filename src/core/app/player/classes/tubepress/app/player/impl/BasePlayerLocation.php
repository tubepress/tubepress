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
class tubepress_app_player_impl_BasePlayerLocation implements tubepress_app_player_api_PlayerLocationInterface
{
    /**
     * @var string[]
     */
    private $_staticTemplatePaths;

    /**
     * @var string[]
     */
    private $_ajaxTemplatePaths;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_displayName;

    /**
     * @var array
     */
    private $_invokingAnchorData;

    public function __construct($name,
                                $displayName,
                                array $staticTemplatePaths,
                                array $ajaxTemplatePaths,
                                $invokingAnchorData = array())
    {
        $this->_staticTemplatePaths = $staticTemplatePaths;
        $this->_ajaxTemplatePaths   = $ajaxTemplatePaths;
        $this->_name                = $name;
        $this->_displayName         = $displayName;
        $this->_invokingAnchorData  = $invokingAnchorData;
    }

    public function onSelectPlayerLocation(tubepress_lib_event_api_EventInterface $event)
    {
        $requestedPlayerLocation = $event->getArgument('playerLocation');

        if ($requestedPlayerLocation === $this) {

            $event->stopPropagation();
        }
    }

    /**
     * @return string The name of this playerLocation. Never empty or null. All alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string The human-readable name of this player location.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return $this->_displayName;
    }

    /**
     * @param tubepress_app_media_item_api_MediaItem $mediaItem
     *
     * @return array An an associative array of attribute names to values that should be included in any
     *               HTML anchors to invoke playback of this media item. e.g. array('href' => 'http://foo.bar/video/id')
     *               will end up like <a href="http://foo.bar/video/id" ...>
     *
     * @api
     * @since 4.0.0
     */
    public function getInvocationAnchorAttributeArray(tubepress_app_media_item_api_MediaItem $mediaItem)
    {
        return $this->_invokingAnchorData;
    }

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplatePathsForStaticContent()
    {
        return $this->_staticTemplatePaths;
    }

    /**
     * @return string[] The paths for the template factory.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplatePathsForAjaxContent()
    {
        return $this->_ajaxTemplatePaths;
    }
}