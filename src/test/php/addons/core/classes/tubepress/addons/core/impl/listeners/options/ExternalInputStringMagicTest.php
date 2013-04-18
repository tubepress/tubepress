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
require_once __DIR__ . '/../AbstractStringMagicFilterTest.php';

class tubepress_addons_core_impl_listeners_options_ExternalInputStringMagicTest extends tubepress_addons_core_impl_listeners_AbstractStringMagicFilterTest
{

    protected function _buildSut()
    {
        return new tubepress_addons_core_impl_listeners_options_ExternalInputStringMagic();
    }

    protected function _performAltering($sut, tubepress_api_event_TubePressEvent $event)
    {
        $sut->onIncomingInput($event);
    }
}