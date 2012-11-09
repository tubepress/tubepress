<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Appends/moves a video the front of the gallery based on the query string parameter.
 */
class tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Video Prepender');
    }

    public function onVideoGalleryPage(tubepress_api_event_TubePressEvent $event)
    {
        $hrps = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();

        $customVideoId = $hrps->getParamValue(tubepress_spi_const_http_ParamName::VIDEO);

        /* they didn't set a custom video id */
        if ($customVideoId == '') {

            return;
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Prepending video %s to the gallery', $customVideoId));
        }

        self::_prependVideo($customVideoId, $event);
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
        foreach ($videos as $video) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($video->getId() == $id) {

                return true;
            }
        }
        return false;
    }

    private function _prependVideo($id, tubepress_api_event_TubePressEvent $event)
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

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug(sprintf('Could not prepend video %s to the gallery: %s', $id, $e->getMessage()));
            }

        }

        /* modify the feed result */
        $event->getSubject()->setVideos($videos);
    }
}
