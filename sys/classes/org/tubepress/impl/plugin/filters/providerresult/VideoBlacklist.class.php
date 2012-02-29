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
 * Filters out any videos that the user has in their blacklist.
 */
class org_tubepress_impl_plugin_filters_providerresult_VideoBlacklist
{
	public function alter_providerResult(org_tubepress_api_provider_ProviderResult $providerResult)
	{
		$videos         = $providerResult->getVideoArray();
		$ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
		$context        = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
		$blacklist      = $context->get(org_tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
		$videosToKeep   = array();
		$blacklistCount = 0;

		foreach ($videos as $video) {

			$id = $video->getId();

			/* keep videos without an ID or that aren't blacklisted */
			if (!isset($id) || $this->_isNotBlacklisted($id, $blacklist)) {
				$videosToKeep[] = $video;
			} else {
			    $blacklistCount++;
			}
		}

		/* modify the feed result */
		$providerResult->setVideoArray($videosToKeep);

		return $providerResult;
	}

	protected function _isNotBlacklisted($id, $blacklist)
	{
		if (strpos($blacklist, $id) !== false) {
	        org_tubepress_impl_log_Log::log('Video Blacklister', 'Video with ID %s is blacklisted. Skipping it.', $id);
	        return false;
	    }
		return true;
	}
}