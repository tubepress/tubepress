<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com).
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_player_impl_SoloOrStaticPlayerLocation extends tubepress_player_impl_JsPlayerLocation
{
    /**
     * @var tubepress_api_url_UrlInterface
     */
    private $_url;

    public function __construct($name, $untranslatedDisplayName,
                                tubepress_api_url_UrlFactoryInterface $urlFactory, $staticTemplateName = null)
    {
        parent::__construct($name, $untranslatedDisplayName, $staticTemplateName);

        $this->_url = $urlFactory->fromCurrent();

        $this->_url->removeSchemeAndAuthority();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesForInvocationAnchor(tubepress_api_media_MediaItem $mediaItem)
    {
        $id = $mediaItem->getId();

        $this->_url->getQuery()->set('tubepress_item', $id);

        return array(
            'rel'  => 'nofollow',
            'href' => $this->_url->toString(),
        );
    }
}
