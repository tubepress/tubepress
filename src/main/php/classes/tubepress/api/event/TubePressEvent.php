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
 * A base class for events thrown around in TubePress.
 */
class tubepress_api_event_TubePressEvent extends ehough_tickertape_impl_GenericEvent
{
    /**
     * @var string The modifiable subject.
     */
    private $_subject;

    /**
     * Encapsulate an event with $subject, $args, and $data.
     *
     * @param mixed $subject   The subject of the event, usually an object.
     * @param array $arguments Arguments to store in the event.
     */
    public function __construct($subject = null, array $arguments = array())
    {
        $this->_subject = $subject;

        $this->setArguments($arguments);
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function setSubject($newSubject)
    {
        $this->_subject = $newSubject;
    }
}
