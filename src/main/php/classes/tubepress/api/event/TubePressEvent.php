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
