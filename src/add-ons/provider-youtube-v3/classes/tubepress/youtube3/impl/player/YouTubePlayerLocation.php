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

class tubepress_youtube3_impl_player_YouTubePlayerLocation implements tubepress_spi_player_PlayerLocationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAttributesForInvocationAnchor(tubepress_api_media_MediaItem $mediaItem)
    {
        return array(

            'rel'    => 'external nofollow',
            'target' => '_blank',
            'href'   => sprintf('https://www.youtube.com/watch?v=%s', $mediaItem->getId()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'youtube';
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return 'from the video\'s original YouTube page';    //>(translatable)<
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
