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
 * A JSON store factory.
 *
 * @api
 * @since 4.1.0
 */
interface tubepress_lib_api_json_JsonStoreFactoryInterface
{
    const _ = 'tubepress_lib_api_json_JsonStoreFactoryInterface';

    /**
     * @api
     * @since 4.1.0
     *
     * @param mixed $json
     *
     * @return tubepress_lib_api_json_JsonStoreInterface
     *
     * @throws InvalidArgumentException If unable to parse JSON
     */
    function newInstance($json);
}