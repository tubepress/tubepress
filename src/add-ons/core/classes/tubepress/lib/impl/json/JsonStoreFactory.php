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
 */
class tubepress_lib_impl_json_JsonStoreFactory implements tubepress_lib_api_json_JsonStoreFactoryInterface
{
    /**
     * @api
     * @since 4.1.0
     *
     * @param string $json
     *
     * @return tubepress_lib_api_json_JsonStoreInterface
     *
     * @throws InvalidArgumentException If unable to parse JSON
     */
    public function newInstance($json)
    {
        return new tubepress_lib_impl_json_JsonStore($json);
    }
}