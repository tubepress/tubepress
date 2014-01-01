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
 * Valid values for autoHide.
 */
class tubepress_addons_youtube_api_const_options_values_YouTube
{
    const AUTOHIDE_SHOW_BOTH              = 'fadeNone';
    const AUTOHIDE_HIDE_BOTH              = 'fadeBoth';
    const AUTOHIDE_HIDE_BAR_SHOW_CONTROLS = 'fadeOnlyProgressBar';

    const CONTROLS_HIDE                 = 'hide';
    const CONTROLS_SHOW_IMMEDIATE_FLASH = 'showImmediate';
    const CONTROLS_SHOW_DELAYED_FLASH   = 'showDelayed';

    const TIMEFRAME_TODAY      = 'today';
    const TIMEFRAME_ALL_TIME   = 'all_time';

    const PLAYER_THEME_DARK  = 'dark';
    const PLAYER_THEME_LIGHT = 'light';

    const SAFESEARCH_NONE     = 'none';
    const SAFESEARCH_MODERATE = 'moderate';
    const SAFESEARCH_STRICT   = 'strict';
}
