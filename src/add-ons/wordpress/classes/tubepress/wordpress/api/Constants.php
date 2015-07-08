<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * WordPress option names.
 */
class tubepress_wordpress_api_Constants
{
    const OPTION_WIDGET_SHORTCODE = 'widget-tagstring';
    const OPTION_WIDGET_TITLE     = 'widget-title';

    const OPTION_WP_PAGE_WHITELIST = 'wpPageWhitelist';
    const OPTION_WP_PAGE_BLACKLIST = 'wpPageBlacklist';

    const EVENT_OPTIONS_PAGE_INVOKED  = 'tubepress.wordpress.event.optionsPageInvoked';

    /**
     * This event is fired after TubePress parses a shortcode.
     *
     * @subject `tubepress_lib_api_shortcode_ShortcodeInterface` The shortcode that was just parsed.
     *
     * @api
     * @since 4.1.11
     */
    const SHORTCODE_PARSED = 'tubepress.app.shortcode.parsed';

    /**
     * @deprecated
     */
    const EVENT_WIDGET_PUBLIC_HTML    = 'tubepress.wordpress.event.widget.publicHtml';

    /**
     * @deprecated
     */
    const EVENT_WIDGET_PRINT_CONTROLS = 'tubepress.wordpress.event.widget.printControls';
}