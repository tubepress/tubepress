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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_options_ui_api_ElementBuilderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_options_ui_api_ElementBuilderInterface';

    /**
     * Builds a new element.
     *
     * @param string $id                      The unique ID of this field.
     * @param string $untranslatedDisplayName Optional display name.
     *
     * @return tubepress_core_options_ui_api_ElementInterface A new instance of the field.
     *
     * @api
     * @since 4.0.0
     */
    function newInstance($id, $untranslatedDisplayName = null);
}