<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_player_impl_listeners_PlayerListener
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var array
     */
    private $_playerLocationNameToInstanceMap;

    private static $_anchorMap = array(

        'href'   => tubepress_api_media_MediaItem::ATTRIBUTE_INVOKING_ANCHOR_HREF,
        'rel'    => tubepress_api_media_MediaItem::ATTRIBUTE_INVOKING_ANCHOR_REL,
        'target' => tubepress_api_media_MediaItem::ATTRIBUTE_INVOKING_ANCHOR_TARGET,
    );

    public function __construct(tubepress_api_options_ContextInterface     $context,
                                tubepress_api_template_TemplatingInterface $templating)
    {
        $this->_context    = $context;
        $this->_templating = $templating;
    }

    public function onNewMediaPage(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var tubepress_api_media_MediaPage
         */
        $mediaPage = $event->getSubject();
        $items     = $mediaPage->getItems();

        if (count($items) === 0) {

            return;
        }

        $activePlayerLocation = $this->_getActivePlayerLocation();

        foreach ($items as $mediaItem) {

            $anchorArgs = $activePlayerLocation->getAttributesForInvocationAnchor($mediaItem);

            if (count($anchorArgs) === 0) {

                continue;
            }

            foreach ($anchorArgs as $anchorAttributeName => $anchorAttributeValue) {

                if (!isset(self::$_anchorMap[$anchorAttributeName])) {

                    continue;
                }

                $mediaItemAttributeName = self::$_anchorMap[$anchorAttributeName];

                $mediaItem->setAttribute($mediaItemAttributeName, $anchorAttributeValue);
            }
        }
    }

    public function onAcceptableValues(tubepress_api_event_EventInterface $event)
    {
        $existing = $event->getSubject();

        if (!is_array($existing)) {

            $existing = array();
        }

        $toAdd = array();

        /*
         * @var tubepress_spi_player_PlayerLocationInterface
         */
        foreach ($this->_playerLocationNameToInstanceMap as $name => $playerLocation) {

            $toAdd[$name] = $playerLocation->getUntranslatedDisplayName();
        }

        asort($toAdd);

        $existing = array_merge($existing, $toAdd);

        $event->setSubject($existing);
    }

    public function onGalleryTemplatePreRender(tubepress_api_event_EventInterface $event)
    {
        $activePlayerLocation = $this->_getActivePlayerLocation();
        $staticTemplateName   = $activePlayerLocation->getStaticTemplateName();

        if ($staticTemplateName === null) {

            return;
        }

        /*
         * @var array
         */
        $templateVars = $event->getSubject();

        /*
         * @var tubepress_api_media_MediaPage
         */
        $mediaPage = $templateVars['mediaPage'];
        $items     = $mediaPage->getItems();

        if (count($items) === 0) {

            return;
        }

        $mediaItem = $items[0];

        $playerTemplateVars = array(
            tubepress_api_template_VariableNames::MEDIA_ITEM => $mediaItem,
        );

        $playerHtml = $this->_templating->renderTemplate('gallery/player/static', $playerTemplateVars);

        $templateVars[tubepress_api_template_VariableNames::PLAYER_HTML] = $playerHtml;

        $event->setSubject($templateVars);
    }

    public function onStaticPlayerTemplateSelection(tubepress_api_event_EventInterface $event)
    {
        $activePlayerLocation = $this->_getActivePlayerLocation();
        $staticTemplateName   = $activePlayerLocation->getStaticTemplateName();

        if ($staticTemplateName === null) {

            return;
        }

        $event->setSubject($staticTemplateName);
    }

    public function onAjaxPlayerTemplateSelection(tubepress_api_event_EventInterface $event)
    {
        $activePlayerLocation = $this->_getActivePlayerLocation();
        $ajaxTemplateName     = $activePlayerLocation->getAjaxTemplateName();

        if ($ajaxTemplateName === null) {

            return;
        }

        $event->setSubject($ajaxTemplateName);
    }

    public function onGalleryInitJs(tubepress_api_event_EventInterface $event)
    {
        $args                 = $event->getSubject();
        $activePlayerLocation = $this->_getActivePlayerLocation();

        if (!isset($args['options']) || !is_array($args['options'])) {

            $args['options'] = array();
        }

        $options = array(
            tubepress_api_options_Names::PLAYER_LOCATION => $activePlayerLocation->getName(),
        );

        $args['options'] = array_merge($args['options'], $options);

        $event->setSubject($args);
    }

    /**
     * @param tubepress_spi_player_PlayerLocationInterface[] $playerLocations
     */
    public function setPlayerLocations(array $playerLocations)
    {
        $this->_playerLocationNameToInstanceMap = array();

        foreach ($playerLocations as $playerLocation) {

            $this->_playerLocationNameToInstanceMap[$playerLocation->getName()] =
                $playerLocation;
        }
    }

    /**
     * @return tubepress_spi_player_PlayerLocationInterface
     *
     * @throws RuntimeException
     */
    private function _getActivePlayerLocation()
    {
        $playerName = $this->_context->get(tubepress_api_options_Names::PLAYER_LOCATION);

        if (!isset($this->_playerLocationNameToInstanceMap[$playerName])) {

            throw new RuntimeException('Unable to find active player location');
        }

        return $this->_playerLocationNameToInstanceMap[$playerName];
    }
}
