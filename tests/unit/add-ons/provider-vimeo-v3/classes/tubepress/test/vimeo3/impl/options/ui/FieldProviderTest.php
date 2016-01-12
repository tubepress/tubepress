<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo3_impl_options_ui_FieldProvider<extended>
 */
class tubepress_test_vimeo2_impl_options_ui_FieldProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_options_ui_FieldProvider
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockField;

    public function onSetup()
    {
        $this->_mockField      = $this->mock('tubepress_api_options_ui_FieldInterface');
        $map = array(
            'x' => array('a', 'b', 'c')
        );

        $this->_sut = new tubepress_vimeo3_impl_options_ui_FieldProvider(
            array($this->_mockField),
            $map
        );
    }

    public function testBasics()
    {
        $id         = $this->_sut->getId();
        $name       = $this->_sut->getUntranslatedDisplayName();
        $categories = $this->_sut->getCategories();
        $fields     = $this->_sut->getFields();

        $this->assertEquals('Vimeo', $name);
        $this->assertEquals('field-provider-vimeo', $id);
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