<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
class org_tubepress_impl_filters_videos_VideoPrepender
{
	public function filter($videos)
	{
		$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
		$qss = $ioc->get('org_tubepress_api_querystring_QueryStringService');

		$customVideoId = $qss->getCustomVideo($_GET);

		/* they didn't set a custom video id */
		if ($customVideoId == '') {
			return $videos;
		}

		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Prepending video %s to the gallery', $customVideoId);
		
		return $this->_prependVideo($ioc, $customVideoId, $videos);
	}

	private function _prependVideo($ioc, $id, $videos)
	{
		/* see if the array already has it */
		if ($this->_videoArrayAlreadyHasVideo($videos, $id)) {
			return $this->_moveVideoUpFront($videos, $id);
		}
	
		$provider = $ioc->get('org_tubepress_video_feed_provider_Provider');
		    try {
		        $video = $provider->getSingleVideo($customVideoId);
		        array_unshift($videos, $video);
		    } catch (Exception $e) {
		        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Could not prepend video %s to the gallery: %s', $customVideoId, $e->getMessage());
		    }
		
		return $videos;

	}

	protected function _getLogPrefix()
	{
		return 'Prepending Video Filter';
	}
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_videos_VideoPrepender');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::VIDEOS_DELIVERY, array($instance, 'filter'));