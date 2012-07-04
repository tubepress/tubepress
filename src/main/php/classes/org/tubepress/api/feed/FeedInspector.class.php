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
 * Examines the feed results.
 */
interface org_tubepress_api_feed_FeedInspector
{
    const _ = 'org_tubepress_api_feed_FeedInspector';

    /**
     * Count the total videos in this feed result.
     *
     * @param unknown $rawFeed The raw video feed (varies depending on provider)
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    function getTotalResultCount($rawFeed);
}
