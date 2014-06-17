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
interface tubepress_core_options_ui_api_Constants
{
    /**
     * This event is fired after TubePress builds the PHP/HTML template for a field on the options page.
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template for the field.
     *
     * @argument <var>field</var> (`tubepress_core_options_ui_api_FieldInterface`): The backing field.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTIONS_UI_FIELD_TEMPLATE = 'tubepress.core.options.ui.event.template.page.field';

    /**
     * This event is fired immediately before TubePress prints the PHP/HTML template for the options page.
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template for the page.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTIONS_UI_PAGE_TEMPLATE = 'tubepress.core.options.ui.event.template.page';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS = 'disabledOptionsPageParticipants';


    const IOC_TAG_OPTIONS_PAGE_TEMPLATE = 'tubepress.core.options.ui.ioc.tag.optionsPageTemplate';

    const OPTIONS_UI_CATEGORY_ADVANCED = 'advanced-category';
}
