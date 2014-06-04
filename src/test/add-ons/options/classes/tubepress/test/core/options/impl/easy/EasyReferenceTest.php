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
 * @covers tubepress_core_options_impl_easy_EasyReference<extended>
 */
class tubepress_test_core_options_impl_easy_EasyReferenceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_options_impl_easy_EasyReference
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    public function onSetup()
    {
        $this->_mockLangUtils = $this->mock(tubepress_api_util_LangUtilsInterface::_);
        $realLangUtils = new tubepress_impl_util_LangUtils();
        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->andReturnUsing(array($realLangUtils, 'isAssociativeArray'));
        $this->_mockLangUtils->shouldReceive('isSimpleArrayOfStrings')->andReturnUsing(array($realLangUtils, 'isSimpleArrayOfStrings'));

        $this->_sut = new tubepress_core_options_impl_easy_EasyReference(array(

            'foo' => 'bar',
            'fuzz' => 5,
            'baz' => new stdClass(),
            'hi' => true
        ), $this->_mockLangUtils);
    }

    public function testNoShortcode()
    {
        $this->_sut->setNoShortcodeOptions(array('fuzz'));
        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode('fuzz'));
    }

    public function testDoNotPersist()
    {
        $this->_sut->setDoNotPersistOptions(array('fuzz'));
        $this->assertFalse($this->_sut->isMeantToBePersisted('fuzz'));
    }

    public function testProOptions()
    {
        $this->_sut->setProOptionNames(array('fuzz'));
        $this->assertTrue($this->_sut->isProOnly('fuzz'));
    }

    public function testProOptionsBadSet()
    {
        $this->setExpectedException('InvalidArgumentException', 'Pro option names must be simple strings.');
        $this->_sut->setProOptionNames(array(5));
    }

    public function testLabelsBadMap1()
    {
        $this->setExpectedException('InvalidArgumentException', 'Label map must be an associative array.');
        $this->_sut->setMapOfOptionNamesToUntranslatedLabels(array('foo'));
    }

    public function testDescriptions()
    {
        $this->_sut->setMapOfOptionNamesToUntranslatedDescriptions(array('foo' => 'FOO', 'nope' => 'NOPE'));

        $this->assertEquals('FOO', $this->_sut->getUntranslatedDescription('foo'));
    }

    public function testLabels()
    {
        $this->_sut->setMapOfOptionNamesToUntranslatedLabels(array('foo' => 'FOO', 'nope' => 'NOPE'));

        $this->assertEquals('FOO', $this->_sut->getUntranslatedLabel('foo'));
    }

    public function testBasics()
    {
        $this->assertTrue($this->_sut->optionExists('foo'));
        $this->assertFalse($this->_sut->optionExists('nope'));
        $this->assertEquals(array('foo', 'fuzz', 'baz', 'hi'), $this->_sut->getAllOptionNames());

        $this->assertEquals(5, $this->_sut->getDefaultValue('fuzz'));
        $this->assertEquals(null, $this->_sut->getDefaultValue('hello'));

        $this->assertTrue($this->_sut->isBoolean('hi'));
        $this->assertFalse($this->_sut->isBoolean('baz'));
        $this->assertFalse($this->_sut->isBoolean('hello'));

        $this->assertFalse($this->_sut->isProOnly('hi'));
        $this->assertFalse($this->_sut->isProOnly('nope'));

        $this->assertTrue($this->_sut->isMeantToBePersisted('hi'));
        $this->assertTrue($this->_sut->isMeantToBePersisted('nope'));

        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('hi'));
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('nope'));

        $this->assertNull($this->_sut->getUntranslatedLabel('hi'));
        $this->assertNull($this->_sut->getUntranslatedLabel('nope'));

        $this->assertNull($this->_sut->getUntranslatedDescription('hi'));
        $this->assertNull($this->_sut->getUntranslatedDescription('nope'));
    }
}