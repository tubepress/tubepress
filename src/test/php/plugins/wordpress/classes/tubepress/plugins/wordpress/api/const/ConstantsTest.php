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
class tubepress_plugins_wordpress_api_const_ConstantsTest extends TubePressUnitTest
{
    function testConstants()
    {
        $toTest = array(

            'tubepress_plugins_wordpress_api_const_options_names_WordPress'	=> array('widget-tagstring', 'widget-title', 'wpPageWhitelist', 'wpPageBlacklist'),
        );

        foreach ($toTest as $className => $expected) {

            $this->assertClassHasConstants($className, $expected);
        }
    }

    private function assertArrayEquality($expected, $actual)
    {
        foreach ($expected as $expectedName) {

            $this->assertTrue(in_array($expectedName, $actual), "Missing expected array value: $expectedName");
        }

        foreach ($actual as $actualName) {

            $this->assertTrue(in_array($actualName, $expected), "Extra array value: $actualName");
        }
    }

    private function assertClassHasConstants($className, array $expected)
    {
        $actual = tubepress_impl_util_LangUtils::getDefinedConstants($className);

        $this->assertArrayEquality($expected, $actual);
    }

}
