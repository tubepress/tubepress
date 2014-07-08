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
 */
class tubepress_app_player_impl_SamePagePlayerLocation extends tubepress_app_player_impl_BasePlayerLocation
{
    /**
     * @var tubepress_lib_url_api_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_lib_util_api_UrlUtilsInterface
     */
    private $_urlUtils;

    public function __construct($name,
                                $label,
                                array $staticTemplatePaths,
                                array $ajaxTemplatePaths,
                                tubepress_lib_url_api_UrlFactoryInterface $urlFactory,
                                tubepress_lib_util_api_UrlUtilsInterface  $urlUtils)
    {
        parent::__construct(

            $name,
            $label,
            $staticTemplatePaths,
            $ajaxTemplatePaths,
            array()
        );

        $this->_urlFactory = $urlFactory;
        $this->_urlUtils   = $urlUtils;
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
        $id         = $mediaItem->getId();
        $currentUrl = $this->_urlFactory->fromCurrent();
        $currentUrl->getQuery()->set(tubepress_lib_http_api_Constants::PARAM_NAME_ITEMID, $id);
        $href       = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($currentUrl);

        return array(
            'rel' => 'nofollow',
            'href' => $href,
        );
    }
}