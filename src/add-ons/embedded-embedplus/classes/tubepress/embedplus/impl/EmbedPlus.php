<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Plays videos with EmbedPlus.
 */
class tubepress_embedplus_impl_EmbedPlus implements tubepress_app_api_embedded_EmbeddedProviderInterface, tubepress_lib_api_template_PathProviderInterface
{
    private static $_OPTIONS = 'options';

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_platform_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_urlFactory = $urlFactory;
    }

    /**
     * @return string[] The names of the media providers that this provider can handle.
     *
     * @api
     * @since 4.0.0
     */
    public function getCompatibleMediaProviderNames()
    {
        return array('youtube');
    }

    /**
     * @return string The name of this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'embedplus';
    }

    /**
     * @return string The template name for this provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateName()
    {
        return 'single/embedded/embedplus';
    }

    /**
     * @return string The display name of this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'EmbedPlus';
    }

    /**
     * @return string[] A set of absolute filesystem directory paths
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateDirectories()
    {
        return array(

            TUBEPRESS_ROOT . '/src/add-ons/embedplus/templates'
        );
    }

    /**
     * @param tubepress_app_api_media_MediaItem $mediaItem
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateVariables(tubepress_app_api_media_MediaItem $mediaItem)
    {
        return array(
            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL =>
                $this->_urlFactory->fromString(sprintf('http://www.youtube.com/embed/%s', $mediaItem->getId()))
        );
    }

    /**
     *
     */
    public function onGalleryInitJs(tubepress_lib_api_event_EventInterface $event)
    {
        $args = $event->getSubject();

        if (!isset($args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL])) {

            return;
        }

        $implementation = $args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL];

        if ($implementation !== 'embedplus') {

            return;
        }

        $existingHeight = $args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_HEIGHT];
        $newHeight      = intval($existingHeight) + 30;
        $args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_HEIGHT] = $newHeight;

        $event->setSubject($args);
    }
}