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
 * @covers tubepress_vimeo_impl_options_ui_VimeoFieldProvider<extended>
 */
class tubepress_test_vimeo_impl_options_ui_FieldProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo_impl_options_ui_VimeoFieldProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockField;

    public function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_lib_translation_api_TranslatorInterface::_);
        $this->_mockField      = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $map = array(
            'x' => array('a', 'b', 'c')
        );

        $this->_sut = new tubepress_vimeo_impl_options_ui_VimeoFieldProvider(
            $this->_mockTranslator,
            array($this->_mockField),
            $map
        );
    }

    public function testBasics()
    {
        $this->_mockTranslator->shouldReceive('_')->with('Vimeo')->andReturn('hi');

        $id         = $this->_sut->getId();
        $name       = $this->_sut->getTranslatedDisplayName();
        $categories = $this->_sut->getCategories();
        $fields     = $this->_sut->getFields();

        $this->assertEquals('hi', $name);
        $this->assertEquals('vimeo-field-provider', $id);
        $this->assertTrue(is_array($categories) && count($categories) === 0);
        $this->assertTrue(is_array($fields) && count($fields) === 1);
        $this->assertSame($fields[0], $this->_mockField);
        $this->assertTrue($this->_sut->isAbleToBeFilteredFromGui());
        $this->assertTrue($this->_sut->fieldsShouldBeInSeparateBoxes());
        $this->assertEquals(array(
            'x' => array('a', 'b', 'c')
        ), $this->_sut->getCategoryIdsToFieldIdsMap());
    }
}