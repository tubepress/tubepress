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
 * A detailed list of the core TubePress events.
 *
 * Each event name can be referred to either by its raw name (e.g. `tubepress.core.cssjs.stylesheets`)
 * or as a constant reference (e.g. `tubepress_app_api_const_event_EventNames::CSS_JS_STYLESHEETS`). The latter
 * simply removes undocumented strings from your code and can help to prevent typos.
 *
 * @package TubePress\Const\Event
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_player_api_Constants
{
    /**
     * This event is fired after a TubePress builds the HTML for a TubePress media player.
     *
     * @subject `string` The player HTML.
     *
     * @argument <var>item</var> (`{@link tubepress_app_media_item_api_MediaItem}`): The item.
     * @argument <var>playerLocation</var> (`tubepress_app_player_api_PlayerLocationInterface`): The TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_PLAYER_HTML = 'tubepress.core.player.html';

    /**
     * This event is fired as TubePress chooses the tubepress_app_player_api_PlayerLocationInterface
     * to generate player HTML.
     *
     * @subject `tubepress_app_player_api_PlayerLocationInterface` The current player location as defined by the context. May be null in some cases.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_PLAYER_SELECT = 'tubepress.core.player.selectPlayerLocation';

    /**
     * This event is fired after TubePress builds the PHP/HTML template for a TubePress
     * media player
     *
     * @subject `tubepress_lib_template_api_TemplateInterface` The player template.
     *
     * @argument <var>item</var> (`{@link tubepress_app_media_item_api_MediaItem}`): The item.
     * @argument <var>playerLocation</var> (`tubepress_app_player_api_PlayerLocationInterface`): The TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_PLAYER_TEMPLATE = 'tubepress.core.player.template';


    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_PLAYER_LOCATION  = 'playerLocation';

    /**
     * @deprecated
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_VAR_NAME = 'playerName';
}
