<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_embedded_EmbeddedPlayerUtilsTest extends PHPUnit_Framework_TestCase
{
    function testBadColor()
    {
        $this->assertEquals('ff88dd', tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('badcolor', 'ff88dd'));
    }

    function testGoodColor()
    {
        $this->assertEquals('eecc33', tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue('eecc33', 'ff88dd'));
    }

    function testBooleanToOneOrZero()
    {
        $this->assertEquals('1', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero(true));
        $this->assertEquals('0', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero(false));
        $this->assertEquals('1', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero('1'));
        $this->assertEquals('0', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero('0'));
    }

    function testBooleanToString()
    {
        $this->assertEquals('true', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString(true));
        $this->assertEquals('false', tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString(false));
    }
}

