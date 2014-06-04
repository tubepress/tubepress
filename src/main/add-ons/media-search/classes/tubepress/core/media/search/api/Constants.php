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
 * or as a constant reference (e.g. `tubepress_core_api_const_event_EventNames::CSS_JS_STYLESHEETS`). The latter
 * simply removes undocumented strings from your code and can help to prevent typos.
 *
 * @package TubePress\Const\Event
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_media_search_api_Constants
{
    /**
     * This event is fired after TubePress builds the HTML for a standard (non-Ajax) search input form.
     *
     * @subject `string` The HTML for the search input.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTML_SEARCH_INPUT = 'tubepress.core.media.search.event.input.html';

    /**
     * This event is fired after TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template for the search input.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_TEMPLATE_SEARCH_INPUT = 'tubepress.core.media.search.event.input.template';




    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_SEARCH_ONLY_USER = 'searchResultsRestrictedToUser';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_SEARCH_PROVIDER = 'searchProvider';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_SEARCH_RESULTS_ONLY = 'searchResultsOnly';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_SEARCH_RESULTS_URL = 'searchResultsUrl';




    /**
     * @api
     * @since 3.1.0
     */
    const OUTPUT_AJAX_SEARCH_INPUT = 'ajaxSearchInput';

    /**
     * @api
     * @since 3.1.0
     */
    const OUTPUT_SEARCH_INPUT = 'searchInput';

    /**
     * @api
     * @since 3.1.0
     */
    const OUTPUT_SEARCH_RESULTS = 'searchResults';
}
