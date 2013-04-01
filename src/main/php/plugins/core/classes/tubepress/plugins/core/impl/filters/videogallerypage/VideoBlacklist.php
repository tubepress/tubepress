<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Filters out any videos that the user has in their blacklist.
 */
class tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Video Blacklister');
    }

	public function onVideoGalleryPage(tubepress_api_event_TubePressEvent $event)
	{
		$videos         = $event->getSubject()->getVideos();
		$context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
		$blacklist      = $context->get(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
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
		$event->getSubject()->setVideos($videosToKeep);
	}

	protected function _isNotBlacklisted($id, $blacklist)
	{
		if (strpos($blacklist, $id) !== false) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug(sprintf('Video with ID %s is blacklisted. Skipping it.', $id));
            }

	        return false;
	    }
		return true;
	}
}