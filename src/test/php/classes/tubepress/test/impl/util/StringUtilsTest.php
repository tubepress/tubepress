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

class tubepress_test_impl_util_StringUtilsTest extends tubepress_test_TubePressUnitTest
{
    public function testStartsWith()
    {
        $this->assertTrue(tubepress_impl_util_StringUtils::startsWith('something', 'some'));
        $this->assertTrue(tubepress_impl_util_StringUtils::startsWith('some', 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::startsWith(array(), 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::startsWith('some', array()));
    }

    public function testEndsWith()
    {
        $this->assertTrue(tubepress_impl_util_StringUtils::endsWith('something', 'thing'));
        $this->assertTrue(tubepress_impl_util_StringUtils::endsWith('some', 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::endsWith(array(), 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::endsWith('some', array()));
    }

    public function testCanReplaceFirstOnlyFirstOccurence()
    {
        $this->assertEquals("zxx", tubepress_impl_util_StringUtils::replaceFirst("x", "z", "xxx"));
    }

    public function testRemoveNewLines()
    {
        $string = "this\r\n\r\n\n\n\nis\r\r\r\na\r\ntest\r\n\r\n";

        $this->assertEquals('thisisatest', tubepress_impl_util_StringUtils::removeNewLines($string));
    }

    public function testRemoveEmptyLines()
    {
        $val = "

        test

        two

        three
        ";

        $this->assertEquals("\n        test\n        two\n        three\n        ", tubepress_impl_util_StringUtils::removeEmptyLines($val));
    }

    public function testStripSlashesDeep()
    {
        $testPatterns = array(

            '\"Hello\"' => '"Hello"',
            '\\"Hi\\"'  => '"Hi"',
            "\\\\\\x"    => 'x',
            "\'you\\'"   => "'you'"
        );

        foreach ($testPatterns as $input => $expected) {

            $actual = tubepress_impl_util_StringUtils::stripslashes_deep($input);

            $this->assertTrue($actual === $expected, "$actual did not equal expected $expected");
        }
    }

    public function testRedactSecrets()
    {
        $this->assertEquals('XXXXXX', tubepress_impl_util_StringUtils::redactSecrets('abdcefabcdef'));
        $this->assertEquals('XXXXXX', tubepress_impl_util_StringUtils::redactSecrets('123456789012'));
        $this->assertEquals('XXXXXX', tubepress_impl_util_StringUtils::redactSecrets('abCDEF789012'));
        $this->assertEquals('abCDEF78901', tubepress_impl_util_StringUtils::redactSecrets('abCDEF78901'));
    }
}