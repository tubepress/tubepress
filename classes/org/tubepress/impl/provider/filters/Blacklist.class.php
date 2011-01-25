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
 * Filters out any videos that the user has in their blacklist.
 */
class org_tubepress_impl_filter_VideosDeliveryBlacklist
{
	public function filter()
	{
		$args = func_get_args();
		$videos = $args[0];

		if (!is_array($videos)) {
			//log
			return $videos;
		}

		$ioc              = org_tubepress_ioc_IocContainer::getInstance();
		$tpom             = $ioc->get('org_tubepress_api_options_OptionsManager');
		$blacklist        = $tpom->get(org_tubepress_api_const_options_Advanced::VIDEO_BLACKLIST);
		$videosToKeep     = array();

		foreach ($videos as $video) {

			$id = $video->getId();
			
			/* keep videos with an ID or that aren't blacklisted */
			if (!isset($id) || $this->_isNotBlacklisted($id, $blacklist)) {
				$videosToKeep[] = $video;				
			}
		}

		unset($videos);
		return $videosToKeep;
	}

	protected function _isNotBlacklisted($id, $blacklist)
	{
		if (strpos($blacklist, $id) !== false) {
	        	org_tubepress_log_Log::log($this->_logPrefix, 'Video with ID %s is blacklisted. Skipping it.', $id);
	        	return false;
	    	}
		return true;
	}
}

$tubepressFilterManager->registerFilter(org_tubepress_api_const_FilterExecutionPoint::VIDEOS_DELIVERY, array(new org_tubepress_impl_filter_VideosDeliveryBlacklist(), 'filter'));
