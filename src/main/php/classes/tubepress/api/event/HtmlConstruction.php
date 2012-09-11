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
 * This event is fired when a TubePress builds *any* HTML. It is fired *after* any other
 * HTML-based events.
 */
class tubepress_api_event_HtmlConstruction extends ehough_tickertape_api_Event
{
    const EVENT_NAME = 'tubepress.api.event.HtmlConstruction';

    /**
     * @var string The HTML.
     */
    public $html;

    public function __construct($html)
    {
        $this->html = $html;
    }
}
