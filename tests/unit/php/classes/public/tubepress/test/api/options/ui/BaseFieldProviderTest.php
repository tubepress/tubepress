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
 * @covers tubepress_api_options_ui_BaseFieldProvider<extended>
 */
class tubepress_test_api_options_ui_BaseFieldProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_options_ui_BaseFieldProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCategory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockField;

    public function onSetup()
    {
        $this->_mockCategory = $this->mock('tubepress_app_api_options_ui_ElementInterface');
        $this->_mockField    = $this->mock('tubepress_app_api_options_ui_FieldInterface');

        $this->_sut = new tubepress_api_options_ui_BaseFieldProvider(
            'some-id',
            'some-untranslated-name',
            false,
            true,
            array($this->_mockCategory),
            array($this->_mockField),
            array('foo' => 'bar')
        );
    }

    public function testBasics()
    {
        $id         = $this->_sut->getId();
        $name       = $this->_sut->getUntranslatedDisplayName();
        $categories = $this->_sut->getCategories();
        $fields     = $this->_sut->getFields();
        $filter     = $this->_sut->isAbleToBeFilteredFromGui();
        $boxes      = $this->_sut->fieldsShouldBeInSeparateBoxes();

        $this->assertEquals('some-untranslated-name', $name);
        $this->assertEquals('some-id', $id);
        $this->assertTrue(is_array($categories) && count($categories) === 1);
        $this->assertSame($categories[0], $this->_mockCategory);
        $this->assertTrue(is_array($fields) && count($fields) === 1);
        $this->assertSame($fields[0], $this->_mockField);
        $this->assertFalse($filter);
        $this->assertTrue($boxes);
        $this->assertEquals(array('foo' => 'bar'), $this->_sut->getCategoryIdsToFieldIdsMap());
    }
}