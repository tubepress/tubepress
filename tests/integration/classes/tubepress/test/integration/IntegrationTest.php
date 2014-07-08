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

abstract class tubepress_test_integration_IntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $lastOptionsUsedForGet;

    protected function get(array $tubePressOptions = array(), $debug = false)
    {
        $debugParams = array(
            'XDEBUG_SESSION_START' => 'true',
            'tubepress_debug'      => $debug ? 'true' : 'false',
        );

        $this->lastOptionsUsedForGet = array_merge($debugParams, array('options' => $tubePressOptions));
        $queryString                 = '?' . http_build_query($this->lastOptionsUsedForGet);

        return file_get_contents('http://localhost:54321/index.php' . $queryString);
    }
}