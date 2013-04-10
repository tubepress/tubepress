<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_embedded_EmbeddedPlayerUtilsTest extends TubePressUnitTest
{
    public function testBadColor()
    {
        $this->assertEquals('ff88dd', tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('badcolor', 'ff88dd'));
    }

    public function testGoodColor()
    {
        $this->assertEquals('eecc33', tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('eecc33', 'ff88dd'));
    }

    public function testBooleanToOneOrZero()
    {
        $this->assertEquals('1', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero(true));
        $this->assertEquals('0', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero(false));
        $this->assertEquals('1', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero('1'));
        $this->assertEquals('0', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero('0'));
    }

    public function testBooleanToString()
    {
        $this->assertEquals('true', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString(true));
        $this->assertEquals('false', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString(false));
    }
}

