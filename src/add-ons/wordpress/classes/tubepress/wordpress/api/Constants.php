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
 * WordPress option names.
 */
class tubepress_wordpress_api_Constants
{
    const OPTION_WIDGET_SHORTCODE = 'widget-tagstring';
    const OPTION_WIDGET_TITLE     = 'widget-title';

    const OPTION_WP_PAGE_WHITELIST = 'wpPageWhitelist';
    const OPTION_WP_PAGE_BLACKLIST = 'wpPageBlacklist';

    const EVENT_OPTIONS_PAGE_INVOKED = 'tubepress.wordpress.event.optionsPageInvoked';

    /**
     * This event is fired when the plugin is activated.
     *
     * @subject `array` An array of the arguments pass to the callback registered with register_activation_hook()
     *                  (usually just an empty array?)
     *
     * @api
     *
     * @since 5.0.2
     */
    const EVENT_PLUGIN_ACTIVATION = 'tubepress.wordpress.plugin_activation';

    /**
     * This event is fired when WordPress passes TubePress a shortcode to parse. The shortcode listener
     * will populate the "result" argument with the HTML that WordPress should insert.
     *
     * @subject `array` An array with:
     *                  0: An associative array of shortcode attributes that WordPress found
     *                  1: The shortcode content (might be empty)
     *                  2: The shortcode keyword
     *
     * @api
     *
     * @since 5.0.2
     */
    const EVENT_SHORTCODE_FOUND = 'tubepress.wordpress.shortcode_found';

    /**
     * This event is fired after TubePress parses a shortcode.
     *
     * @subject `tubepress_api_shortcode_ShortcodeInterface` The shortcode that was just parsed.
     *
     * @api
     *
     * @since 4.1.11
     */
    const SHORTCODE_PARSED = 'tubepress.app.shortcode.parsed';

    const OPTION_AUTOPOST_ENABLE           = 'wpAutoPostEnable';
    const OPTION_AUTOPOST_AUTHOR           = 'wpAutoPostAuthor';
    const OPTION_AUTOPOST_DATE_SOURCE      = 'wpAutoPostDateSource';
    const OPTION_AUTOPOST_TITLE_FORMAT     = 'wpAutoPostTitleTemplate';
    const OPTION_AUTOPOST_CONTENT_TEMPLATE = 'wpAutoPostContentTemplate';
    const OPTION_AUTOPOST_POST_STATUS      = 'wpAutoPostStatus';
    const OPTION_AUTOPOST_TYPE             = 'wpAutoPostType';
    const OPTION_AUTOPOST_ALLOW_COMMENTS   = 'wpAutoPostAllowComments';
    const OPTION_AUTOPOST_ALLOW_PING       = 'wpAutoPostAllowPings';
    const OPTION_AUTOPOST_PASSWORD         = 'wpAutoPostPassword';
    const OPTION_AUTOPOST_TAGS             = 'wpAutoPostTags';
    const OPTION_AUTOPOST_CATEGORIES       = 'wpAutoPostCategories';
    const OPTION_AUTOPOST_PAGE_TEMPLATE    = 'wpAutoPostPageTemplate';
    const OPTION_AUTOPOST_META_MAP         = 'wpAutoPostMetaTemplate';

    const AUTOPOST_DATA_SOURCE_UPLOAD    = 'upload';
    const AUTOPOST_DATA_SOURCE_DISCOVERY = 'discovery';

    /**
     * @deprecated
     */
    const EVENT_WIDGET_PUBLIC_HTML = 'tubepress.wordpress.event.widget.publicHtml';

    /**
     * @deprecated
     */
    const EVENT_WIDGET_PRINT_CONTROLS = 'tubepress.wordpress.event.widget.printControls';
}
