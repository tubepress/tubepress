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
 * A form element that appears and participates in the options page.
 *
 * @api
 * @since 4.1.11
 */
interface tubepress_api_options_ui_MultiSourceFieldInterface extends tubepress_api_options_ui_FieldInterface
{
    /**
     * @param $prefix
     * @param tubepress_api_options_PersistenceInterface $persistence
     *
     * @return tubepress_api_options_ui_FieldInterface
     */
    function cloneForMultiSource($prefix, tubepress_api_options_PersistenceInterface $persistence);
}