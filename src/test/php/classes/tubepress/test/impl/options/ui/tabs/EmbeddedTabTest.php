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
class tubepress_test_impl_options_ui_tabs_EmbeddedTabTest extends tubepress_test_impl_options_ui_tabs_AbstractTabTest
{

    protected function _getRawTitle()
    {
        return 'Player';
    }

    protected function _buildSut()
    {
        return new tubepress_impl_options_ui_tabs_EmbeddedTab('path to template');
    }
}