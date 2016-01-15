<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_spi_embedded_EmbeddedProviderInterface
{
    /**
     * @return string[] The names of the media providers that this provider can handle.
     *
     * @api
     * @since 4.0.0
     */
    function getCompatibleMediaProviderNames();

    /**
     * @return string The name of this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The template name for this provider.
     *
     * @api
     * @since 4.0.0
     */
    function getTemplateName();

    /**
     * @param tubepress_api_media_MediaItem $mediaItem
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getTemplateVariables(tubepress_api_media_MediaItem $mediaItem);

    /**
     * @return string The display name of this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    function getUntranslatedDisplayName();
}