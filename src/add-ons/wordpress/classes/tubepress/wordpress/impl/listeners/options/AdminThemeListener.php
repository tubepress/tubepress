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
class tubepress_wordpress_impl_listeners_options_AdminThemeListener
{
    public function onDefaultValue(tubepress_api_event_EventInterface $event)
    {
        $event->setSubject('tubepress/wordpress');
    }
}
