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
 * @covers tubepress_http_impl_NonceManager
 */
class tubepress_test_http_impl_NonceManagerTest extends tubepress_api_test_TubePressUnitTest
{
    public function testValidNonce()
    {
        $manager = new tubepress_http_impl_NonceManager();
        $val     = $manager->getNonce();

        $this->assertTrue(is_string($val));
        $this->assertTrue(strlen($val) === 32);

        $this->assertTrue($manager->isNonceValid($val));
    }

    public function testInvalidNonce()
    {
        $manager = new tubepress_http_impl_NonceManager();
        $this->assertFalse($manager->isNonceValid('bla bla'));
    }
}

