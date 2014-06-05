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
 * Appends/moves a video the front of the gallery based on the query string parameter.
 */
class tubepress_core_media_provider_impl_listeners_page_ItemPrepender
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_core_media_provider_api_CollectorInterface
     */
    private $_collector;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_core_http_api_RequestParametersInterface $requestParams,
                                tubepress_core_media_provider_api_CollectorInterface     $collector)
    {
        $this->_logger        = $logger;
        $this->_requestParams = $requestParams;
        $this->_collector     = $collector;
    }

    public function onVideoGalleryPage(tubepress_core_event_api_EventInterface $event)
    {
        $customVideoId = $this->_requestParams->getParamValue(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO);
        $shouldLog     = $this->_logger->isEnabled();

        /* they didn't set a custom video id */
        if ($customVideoId == '') {

            return;
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Prepending video %s to the gallery', $customVideoId));
        }

        $this->_prependVideo($customVideoId, $event, $shouldLog);
    }

    private function _prependVideo($id, tubepress_core_event_api_EventInterface $event, $shouldLog)
    {
        $videos = $event->getSubject()->getItems();

        /* see if the array already has it */
        if (self::_videoArrayAlreadyHasVideo($videos, $id)) {

            $videos = $this->_moveVideoUpFront($videos, $id);

        } else {

            $video = $this->_collector->collectSingle($id);

            if ($video) {

                array_unshift($videos, $video);
            }
        }

        /* modify the feed result */
        $event->getSubject()->setItems($videos);
    }

    private function _moveVideoUpFront($videos, $targetId)
    {
        /**
         * @var $videos tubepress_core_media_item_api_MediaItem[]
         */
        for ($x = 0; $x < count($videos); $x++) {

            $id = $this->_getIdFromItem($videos[$x]);
;
            if ($id == $targetId) {

                $saved = $videos[$x];

                unset($videos[$x]);

                array_unshift($videos, $saved);

                break;
            }
        }

        return $videos;
    }

    private function _videoArrayAlreadyHasVideo($videos, $targetId)
    {
        /**
         * @var $video tubepress_core_media_item_api_MediaItem
         */
        foreach ($videos as $video) {

            $id = $this->_getIdFromItem($video);

            if ($id == $targetId) {

                return true;
            }
        }
        return false;
    }

    private function _getIdFromItem(tubepress_core_media_item_api_MediaItem $item)
    {
        /**
         * @var $provider tubepress_core_media_provider_api_MediaProviderInterface
         */
        $provider = $item->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER);

        return $item->getAttribute($provider->getAttributeNameOfItemId());
    }

}
