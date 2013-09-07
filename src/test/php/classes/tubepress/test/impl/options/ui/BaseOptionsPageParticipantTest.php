<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_options_ui_BaseOptionsPageParticipant
 */
class tubepress_test_impl_options_ui_BaseOptionsPageParticipantTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_BaseOptionsPageParticipant
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockCategories;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFields;

    public function onSetup()
    {
        $this->_mockMessageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $mockCategory = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageItemInterface');
        $this->_mockCategories = array($mockCategory);

        $mockField = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageFieldInterface');
        $this->_mockFields = array($mockField);

        $map = array(

            'category1' => array('field1')
        );

        $this->_sut = new tubepress_impl_options_ui_BaseOptionsPageParticipant(

            'id',
            'display name',
            $this->_mockCategories,
            $this->_mockFields,
            $map
        );
    }

    public function testGetMap()
    {
        $result = $this->_sut->getCategoryIdsToFieldIdsMap();

        $this->assertEquals(array('category1' => array('field1')), $result);
    }

    public function testGetCategories()
    {
        $this->assertEquals($this->_mockCategories, $this->_sut->getCategories());
    }

    public function testGetFields()
    {
        $this->assertEquals($this->_mockFields, $this->_sut->getFields());
    }
}
