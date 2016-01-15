<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Builds fields!
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_options_ui_FieldBuilderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_options_ui_FieldBuilderInterface';

    /**
     * Builds a new field.
     *
     * @param string $id      The unique ID of this field.
     * @param string $type    The type of the field. (e.g. text, radio, dropdown, multi, etc)
     * @param array  $options An optional array of options to contruct the field.
     *
     * @return tubepress_api_options_ui_FieldInterface A new instance of the field.
     *
     * @throws InvalidArgumentException If unable to build the given type.
     *
     * @api
     * @since 4.0.0
     */
    function newInstance($id, $type, array $options = array());
}