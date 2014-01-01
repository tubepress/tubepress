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
class tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Video Prepender');
    }

    public function onVideoGalleryPage(tubepress_api_event_EventInterface $event)
    {
        $hrps          = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $customVideoId = $hrps->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);
        $shouldLog     = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /* they didn't set a custom video id */
        if ($customVideoId == '') {

            return;
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Prepending video %s to the gallery', $customVideoId));
        }

        self::_prependVideo($customVideoId, $event, $shouldLog);
    }

    private static function _moveVideoUpFront($videos, $id)
    {
        for ($x = 0; $x < count($videos); $x++) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($videos[$x]->getId() == $id) {

                $saved = $videos[$x];

                unset($videos[$x]);

                array_unshift($videos, $saved);

                break;
            }
        }
        return $videos;
    }

    private function _videoArrayAlreadyHasVideo($videos, $id)
    {
        /**
         * @var $video tubepress_api_video_Video
         */
        foreach ($videos as $video) {

            if ($video->getId() == $id) {

                return true;
            }
        }
        return false;
    }

    private function _prependVideo($id, tubepress_api_event_EventInterface $event, $shouldLog)
    {
        $videos = $event->getSubject()->getVideos();

        /* see if the array already has it */
        if (self::_videoArrayAlreadyHasVideo($videos, $id)) {

            $videos = self::_moveVideoUpFront($videos, $id);

            $event->getSubject()->setVideos($videos);

            return;
        }

        try {

            $provider = tubepress_impl_patterns_sl_ServiceLocator::getVideoCollector();

            $video = $provider->collectSingleVideo($id);

            array_unshift($videos, $video);

        } catch (Exception $e) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Could not prepend video %s to the gallery: %s', $id, $e->getMessage()));
            }

        }

        /* modify the feed result */
        $event->getSubject()->setVideos($videos);
    }
}
