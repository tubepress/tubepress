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
 * @covers tubepress_addons_core_impl_options_MetaOptionNameService<extended>
 */
class tubepress_test_addons_core_impl_options_MetaOptionNameServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_options_MetaOptionNameService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockVideoProviders;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_options_MetaOptionNameService();

        $this->_buildMockVideoProviders();
        $this->_sut->setVideoProviders($this->_mockVideoProviders);
    }

    private function _buildMockVideoProviders()
    {
        $this->_mockVideoProviders = array();

        $mockProvider1 = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider1->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('a', 'b', 'c'));
        $mockProvider1->shouldReceive('getFriendlyName')->once()->andReturn('Mock 1');

        $mockProvider2 = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $mockProvider2->shouldReceive('getAdditionalMetaNames')->once()->andReturn(array('x', 'y', 'z'));
        $mockProvider2->shouldReceive('getFriendlyName')->once()->andReturn('Mock 2');

        $this->_mockVideoProviders[] = $mockProvider1;
        $this->_mockVideoProviders[] = $mockProvider2;
    }

    public function testGetAllMetaOptionNames()
    {
        $actual = $this->_sut->getAllMetaOptionNames();

        $this->assertEquals(array_merge($this->_getCoreMetaNames(), array('a', 'b', 'c', 'x', 'y', 'z')), $actual);
    }

    public function testGetCoreMetaOptionNames()
    {
        $actual = $this->_sut->getCoreMetaOptionNames();

        $this->assertEquals($this->_getCoreMetaNames(), $actual);
    }

    public function testGetMapOfFriendlyProviderNamesToMetaOptionNames()
    {
        $actual = $this->_sut->getMapOfFriendlyProviderNameToMetaOptionNames();

        $this->assertEquals(array(

            'Mock 1' => array('a', 'b', 'c'),
            'Mock 2' => array('x', 'y', 'z')
        ), $actual);
    }

    private function _getCoreMetaNames()
    {
        return array(

            tubepress_api_const_options_names_Meta::AUTHOR,
            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::UPLOADED,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::KEYWORDS,
            tubepress_api_const_options_names_Meta::LENGTH,
            tubepress_api_const_options_names_Meta::TITLE,
            tubepress_api_const_options_names_Meta::URL,
            tubepress_api_const_options_names_Meta::VIEWS,
        );
    }
}