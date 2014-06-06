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
 * @api
 * @since 4.0.0
 */
interface tubepress_core_embedded_api_Constants
{
    /**
     * This event is fired when TubePress builds the PHP/HTML template for an embedded
     * media player.
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The embedded media player template.
     *
     * @argument <var>itemId</var> (`string`): The ID of the item to be viewed. (since 4.0.0)
     * @argument <var>mediaProvider</var> (`tubepress_core_media_provider_api_MediaProviderInterface`): The item's media provider. (since 4.0.0)
     * @argument <var>dataUrl</var> (`tubepress_core_url_api_UrlInterface`): The embedded data URL. (since 4.0.0)
     * @argument <var>embeddedProvider</var> (`tubepress_core_embedded_api_EmbeddedProviderInterface`): The embedded provider. (since 4.0.0)
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_TEMPLATE_EMBEDDED = 'tubepress.core.embedded.template';

    /**
     * This event is fired when TubePress builds the HTML for an embedded media player.
     *
     * @subject `string` The HTML for the embedded media player.
     *
     * @argument <var>itemId</var> (`string`): The ID of the item to be viewed. (since 4.0.0)
     * @argument <var>mediaProvider</var> (`tubepress_core_media_provider_api_MediaProviderInterface`): The item's media provider. (since 4.0.0)
     * @argument <var>dataUrl</var> (`tubepress_core_url_api_UrlInterface`): The embedded data URL. (since 4.0.0)
     * @argument <var>embeddedProvider</var> (`tubepress_core_embedded_api_EmbeddedProviderInterface`): The embedded provider. (since 4.0.0)
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTML_EMBEDDED = 'tubepress.core.embedded.html';



    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_AUTOPLAY         = 'autoplay';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_EMBEDDED_HEIGHT  = 'embeddedHeight';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_EMBEDDED_WIDTH   = 'embeddedWidth';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_ENABLE_JS_API = 'enableJsApi';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_LAZYPLAY         = 'lazyPlay';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_LOOP             = 'loop';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_PLAYER_IMPL      = 'playerImplementation';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_SHOW_INFO        = 'showInfo';




    /**
     * @api
     * @since 3.1.0
     */
    const EMBEDDED_IMPL_PROVIDER_BASED = 'provider_based';
}
