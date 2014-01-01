<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * A base class for events thrown around in TubePress.
 *
 * @api
 * @since 3.1.0
 */
class tubepress_spi_event_EventBase extends ehough_tickertape_GenericEvent implements tubepress_api_event_EventInterface
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
