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
 * This event is fired when a TubePress builds the gallery initialization JS code.
 */
class tubepress_api_event_GalleryInitJsConstruction extends ehough_tickertape_impl_GenericEvent
{
    const EVENT_NAME = 'core.GalleryInitJsConstruction';

    /**
     * @var string The init params.
     */
    private $_array;

    /**
     * Encapsulate an event with $subject, $args, and $data.
     *
     * @param mixed $html      The subject of the event, usually an object.
     * @param array $arguments Arguments to store in the event.
     */
    public function __construct($html = null, array $arguments = array())
    {
        $this->_array = $html;

        $this->setArguments($arguments);
    }

    public function getSubject()
    {
        return $this->_array;
    }

    public function setParams(array $newValue)
    {
        $this->_array = $newValue;
    }
}
