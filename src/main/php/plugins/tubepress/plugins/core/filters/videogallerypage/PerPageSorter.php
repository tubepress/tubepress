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
 * Shuffles videos on request.
 */
class tubepress_plugins_core_filters_videogallerypage_PerPageSorter
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Per-page Sorter');
    }

	public function onVideoGalleryPage(tubepress_api_event_VideoGalleryPageConstruction $event)
	{
		$context          = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
		$perPageSortOrder = $context->get(tubepress_api_const_options_names_Feed::PER_PAGE_SORT);
		$feedSortOrder    = $context->get(tubepress_api_const_options_names_Feed::ORDER_BY);

		/** No sort requested? */
		if ($perPageSortOrder === tubepress_api_const_options_values_PerPageSortValue::NONE) {

            if ($this->_logger->isDebugEnabled()) {
                
                $this->_logger->debug('Requested per-page sort order is "none". Not applying per-page sorting.');
            }

		    return;
		}

		/** Grab a handle to the videos. */
		$videos = $event->getSubject()->getVideos();

		if ($perPageSortOrder === tubepress_api_const_options_values_PerPageSortValue::RANDOM) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('Shuffling videos');
            }

		    shuffle($videos);

		} else {

		    /** Determine the sort method name. */
		    $sortCallback = '_' . $perPageSortOrder . '_compare';

		    /** If we have a sorter, use it. */
		    if (method_exists($this, $sortCallback)) {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug(sprintf('Now sorting %s videos on page (%s)', count($videos), $perPageSortOrder));
                }

		        uasort($videos, array($this, $sortCallback));

		    } else {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug(sprintf('No sort available for this page (%s)', $perPageSortOrder));
                }
		    }
		}

		$videos = array_values($videos);

		/** Modify the feed result. */
		$event->getSubject()->setVideos($videos);
	}

	private function _commentCount_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return $this->_compareGreatestToLeast($this->_safeIntVal($one->getCommentCount()), $this->_safeIntVal($two->getCommentCount()));
	}

	private function _duration_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return $this->_compareGreatestToLeast($one->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS),
            $two->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS));
	}

	private function _newest_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return $this->_compareGreatestToLeast($one->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME),
            $two->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
	}

	private function _oldest_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return $this->_compareLeastToGreatest($one->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME),
            $two->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
	}

	private function _rating_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return $this->_compareGreatestToLeast(floatval($one->getRatingAverage()), floatval($two->getRatingAverage()));
	}

	private function _title_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return strcmp($one->getTitle(), $two->getTitle());
	}

	private function _viewCount_compare(tubepress_api_video_Video $one, tubepress_api_video_Video $two)
	{
	    return $this->_compareGreatestToLeast($this->_safeIntVal($one->getViewCount()), $this->_safeIntVal($two->getViewCount()));
	}

	private function _safeIntVal($val)
	{
	    if (is_string($val)) {

	        $x = str_replace(',', '', $val);

	    } else {

	        $x = $val;
	    }

	    return intval($x);
	}

	private function _compareLeastToGreatest($one, $two)
	{
	    if ($one == $two) {

	        return 0;
	    }

	    return $one < $two ? -1 : 1;
	}

	private function _compareGreatestToLeast($one, $two)
	{
	    if ($one == $two) {

	        return 0;
	    }

	    return $one > $two ? -1 : 1;
	}
}