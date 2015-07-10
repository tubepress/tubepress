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
 * @covers tubepress_util_impl_StringUtils
 */
class tubepress_test_impl_util_StringUtilsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_util_impl_StringUtils
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_util_impl_StringUtils();
    }
    
    public function testStartsWith()
    {
        $this->assertTrue($this->_sut->startsWith('something', 'some'));
        $this->assertTrue($this->_sut->startsWith('some', 'some'));
        $this->assertFalse($this->_sut->startsWith(array(), 'some'));
        $this->assertFalse($this->_sut->startsWith('some', array()));
    }

    public function testEndsWith()
    {
        $this->assertTrue($this->_sut->endsWith('something', 'thing'));
        $this->assertTrue($this->_sut->endsWith('some', 'some'));
        $this->assertFalse($this->_sut->endsWith(array(), 'some'));
        $this->assertFalse($this->_sut->endsWith('some', array()));
    }

    public function testCanReplaceFirstOnlyFirstOccurence()
    {
        $this->assertEquals("zxx", $this->_sut->replaceFirst("x", "z", "xxx"));
    }

    public function testRemoveNewLines()
    {
        $string = "this\r\n\r\n\n\n\nis\r\r\r\na\r\ntest\r\n\r\n";

        $this->assertEquals('thisisatest', $this->_sut->removeNewLines($string));
    }

    public function testRemoveEmptyLines()
    {
        $val = "

        test

        two

        three
        ";

        $this->assertEquals("\n        test\n        two\n        three\n        ", $this->_sut->removeEmptyLines($val));
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

            $actual = $this->_sut->stripslashes_deep($input);

            $this->assertTrue($actual === $expected, "$actual did not equal expected $expected");
        }
    }

    public function testRedactSecrets()
    {
        $this->assertEquals('XXXXXX', $this->_sut->redactSecrets('abdcefabcdef'));
        $this->assertEquals('XXXXXX', $this->_sut->redactSecrets('123456789012'));
        $this->assertEquals('XXXXXX', $this->_sut->redactSecrets('abCDEF789012'));
        $this->assertEquals('abCDEF78901', $this->_sut->redactSecrets('abCDEF78901'));
    }
}