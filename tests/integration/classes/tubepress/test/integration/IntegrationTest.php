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

abstract class tubepress_test_integration_IntegrationTest extends PHPUnit_Framework_TestCase
{
    private $_options;

    public function setUp()
    {
        $this->_options = array();
    }

    protected function setOptions(array $opts)
    {
        $this->_options = $opts;
    }

    protected function get($debug = false)
    {
        $debugParams = array(
            'XDEBUG_SESSION_START' => 'true',
            'tubepress_debug'      => $debug ? 'true' : 'false',
        );

        $opts        = array_merge($debugParams, array('options' => $this->_options));
        $queryString = '?' . http_build_query($opts);

        return file_get_contents('http://localhost:54321/index.php' . $queryString);
    }
}