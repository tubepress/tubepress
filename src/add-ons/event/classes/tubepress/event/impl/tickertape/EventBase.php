<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * A base class for events thrown around in TubePress.
 */
class tubepress_event_impl_tickertape_EventBase extends \Symfony\Component\EventDispatcher\GenericEvent implements tubepress_api_event_EventInterface
{
    public function setSubject($newSubject)
    {
        $this->subject = $newSubject;
    }
}
