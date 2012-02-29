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
 * Calculates video provider in use.
 */
interface org_tubepress_api_provider_ProviderCalculator
{
    const _ = 'org_tubepress_api_provider_ProviderCalculator';

    /**
     * Determine the current video provider.
     *
     * @return string 'youtube', 'vimeo', or 'directory'
     */
    function calculateCurrentVideoProvider();

    /**
     * Determine the provider of the given video ID.
     *
     * @param string $videoId The ID of the video to examine.
     *
     * @return string 'youtube', 'vimeo', or 'directory'
     */
    function calculateProviderOfVideoId($videoId);
}
