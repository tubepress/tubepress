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
 * Pulls out info from $_GET or $_POST.
 */
interface tubepress_spi_http_HttpRequestParameterService
{
    const _ = 'tubepress_spi_http_HttpRequestParameterService';

    /**
     * Gets the parameter value from PHP's $_GET or $_POST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed The raw value of the parameter. Can be anything that would
     *               otherwise be found in PHP's $_GET or $_POST array. Returns null
     *               if the parameter is not set on this request.
     */
    function getParamValue($name);

    /**
     * Gets the parameter value from PHP's $_GET or $_POST array. If the hasParam($name) returs false, this
     *  behaves just like getParamvalue($name). Otherwise, if the raw parameter value is numeric, a conversion
     *  will be attempted.
     *
     * @param string $name    The name of the parameter.
     * @param int    $default The default value is the raw value is not integral.
     *
     * @return mixed The raw value of the parameter. Can be anything that would
     *               otherwise be found in PHP's $_GET or $_POST array. Returns null
     *               if the parameter is not set on this request.
     */
    function getParamValueAsInt($name, $default);

    /**
     * Determines if the parameter is set in PHP's $_GET or $_POST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed True if the parameter is found in PHP's $_GET or $_POST array, false otherwise.
     */
    function hasParam($name);

    /**
     * Returns a map of param name => param value for ALL parameters in the request.
     *
     * @return array A map of param name => param value for ALL parameters in the request.
     */
    function getAllParams();
}
