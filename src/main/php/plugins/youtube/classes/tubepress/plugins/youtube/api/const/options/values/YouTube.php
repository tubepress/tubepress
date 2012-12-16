<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Valid values for autoHide.
 */
class tubepress_plugins_youtube_api_const_options_values_YouTube
{
    const AUTOHIDE_SHOW_BOTH              = 'youtubePlayerFadeNone';
    const AUTOHIDE_HIDE_BOTH              = 'youtubePlayerFadeBoth';
    const AUTOHIDE_HIDE_BAR_SHOW_CONTROLS = 'youtubePlayerFadeProgressBar';

    const CONTROLS_HIDE                 = 'youtubeControlsHide';
    const CONTROLS_SHOW_IMMEDIATE_FLASH = 'youtubeControlsShowImmediate';
    const CONTROLS_SHOW_DELAYED_FLASH   = 'youtubeControlsShowDelayed';

    const TIMEFRAME_TODAY      = 'today';
    const TIMEFRAME_THIS_WEEK  = 'this_week';
    const TIMEFRAME_THIS_MONTH = 'this_month';
    const TIMEFRAME_ALL_TIME   = 'all_time';

    const PLAYER_THEME_DARK  = 'youtubePlayerThemeDark';
    const PLAYER_THEME_LIGHT = 'youtubePlayerThemeLight';

    const SAFESEARCH_NONE     = 'none';
    const SAFESEARCH_MODERATE = 'moderate';
    const SAFESEARCH_STRICT   = 'strict';
}
