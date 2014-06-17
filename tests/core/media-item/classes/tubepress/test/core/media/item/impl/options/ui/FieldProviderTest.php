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
 * @covers tubepress_core_media_item_impl_options_ui_FieldProvider<extended>
 */
class tubepress_test_core_media_item_impl_options_ui_FieldProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_item_impl_options_ui_FieldProvider
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    public function onSetup()
    {
        $this->_mockCategory   = $this->mock('tubepress_core_options_ui_api_ElementInterface');
        $this->_mockField      = $this->mock('tubepress_core_options_ui_api_FieldInterface');
        $this->_mockTranslator = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);
        $this->_mockTranslator->shouldReceive('_')->once()->with('Meta')->andReturn('sdf');
        $map = array(
            'x' => array('a', 'b', 'c')
        );

        $this->_sut = new tubepress_core_media_item_impl_options_ui_FieldProvider(
            $this->_mockTranslator,
            array($this->_mockCategory),
            array($this->_mockField),
            $map
        );
    }

    public function testBasics()
    {
        $id         = $this->_sut->getId();
        $name       = $this->_sut->getTranslatedDisplayName();
        $categories = $this->_sut->getCategories();
        $fields     = $this->_sut->getFields();

        $this->assertEquals('sdf', $name);
        $this->assertEquals('media-item-field-provider', $id);
        $this->assertTrue(is_array($categories) && count($categories) === 1);
        $this->assertSame($categories[0], $this->_mockCategory);
        $this->assertTrue(is_array($fields) && count($fields) === 1);
        $this->assertSame($fields[0], $this->_mockField);
        $this->assertFalse($this->_sut->isAbleToBeFilteredFromGui());
        $this->assertFalse($this->_sut->fieldsShouldBeInSeparateBoxes());
        $this->assertEquals(array(
            'x' => array('a', 'b', 'c')
        ), $this->_sut->getCategoryIdsToFieldIdsMap());
    }
}