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
class bootstrapClassMapTest extends tubepress_test_TubePressUnitTest
{
    public function testClassMapValidity()
    {
        $classMap = require dirname(__FILE__) . '/../../../../main/php/scripts/classmaps/bootstrap.php';

        $this->assertTrue(is_array($classMap));

        $this->assertTrue(tubepress_impl_util_LangUtils::isAssociativeArray($classMap));

        foreach ($classMap as $className => $path) {

            $this->assertTrue(is_readable($path) && is_file($path), "$path is not readable. Fix it!");
        }
    }
}