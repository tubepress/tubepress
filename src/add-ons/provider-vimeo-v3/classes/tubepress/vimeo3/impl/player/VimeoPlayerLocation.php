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

class tubepress_vimeo3_impl_player_VimeoPlayerLocation implements tubepress_spi_player_PlayerLocationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vimeo';
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return 'from the video\'s original Vimeo page';                 //>(translatable)<
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesForInvocationAnchor(tubepress_api_media_MediaItem $mediaItem)
    {
        return array(
            'target' => '_blank',
            'rel'    => 'external nofollow',
            'href'   => sprintf('https://vimeo.com/%d', $mediaItem->getId()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTemplateName()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxTemplateName()
    {
        return null;
    }
}
