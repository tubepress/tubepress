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
 * Performs filtering on potentially malicious or typo'd string input.
 */
class tubepress_addons_core_impl_listeners_options_ExternalInputStringMagic extends tubepress_addons_core_impl_listeners_AbstractStringMagicFilter
{
    function onIncomingInput(tubepress_api_event_EventInterface $event)
    {
        $this->_magic($event);
    }
}
