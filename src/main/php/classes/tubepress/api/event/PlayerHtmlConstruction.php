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
 * This event is fired when a TubePress builds the HTML for a TubePress
 * "player".
 */
class tubepress_api_event_PlayerHtmlConstruction extends ehough_tickertape_api_Event
{
    const EVENT_NAME = 'tubepress.api.event.PlayerHtmlConstruction';

    /**
     * @var string The HTML for the TubePress player.
     */
    public $playerHtml;

    /**
     * @var tubepress_api_video_Video The video to be played.
     */
    public $video;

    /**
     * @var string The name of the video provider (e.g. "vimeo" or "youtube")
     */
    public $providerName;

    /**
     * @var string The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    public $playerName;

    public function __construct(

                                  $html,
        tubepress_api_video_Video $video,
                                  $providerName,
                                  $playerName)
    {
        $this->playerHtml   = $html;
        $this->video        = $video;
        $this->providerName = $providerName;
        $this->playerName   = $playerName;
    }
}
