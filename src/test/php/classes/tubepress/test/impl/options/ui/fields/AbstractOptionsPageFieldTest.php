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
 *
 */
abstract class tubepress_test_impl_options_ui_fields_AbstractOptionsPageFieldTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;


    public final function onSetup()
    {
        $this->_mockMessageService              = $this->createMockSingletonService(tubepress_api_translation_TranslatorInterface::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);

        $this->doOnSetup();

        $this->_sut = $this->buildSut();
    }

    public function testGetId()
    {
        $result = $this->_sut->getId();

        $this->assertEquals($this->getExpectedFieldId(), $result);
    }

    public function testGetTranslatedDisplayName()
    {
        $expected = $this->getExpectedUntranslatedFieldLabel();

        if ($expected) {

            $expected = '<<' . $this->getExpectedUntranslatedFieldLabel() . '>>';

            $this->_mockMessageService->shouldReceive('_')->once()->with($this->getExpectedUntranslatedFieldLabel())->andReturn($expected);

        } else {

            $expected = '';
        }

        $result = $this->_sut->getTranslatedDisplayName();

        $this->assertEquals($expected, $result);
    }

    public function testGetTranslatedDescription()
    {
        $expected = $this->getExpectedUntranslatedFieldDescription();

        if ($expected) {

            $expected = "<<$expected>>";
            $this->_mockMessageService->shouldReceive('_')->once()->with($this->getExpectedUntranslatedFieldDescription())->andReturn($expected);

        } else {

            $expected = '';
        }

        $this->prepareForGetDescription();

        $result = $this->_sut->getTranslatedDescription();

        $this->assertEquals($expected, $result);
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected function getSut()
    {
        return $this->_sut;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockHttpRequestParameterService()
    {
        return $this->_mockHttpRequestParameterService;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockStorageManager()
    {
        return $this->_mockStorageManager;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockMessageService()
    {
        return $this->_mockMessageService;
    }

    protected function doOnSetup()
    {
        //override point
    }

    protected function prepareForGetDescription()
    {
        //override point
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected abstract function buildSut();

    protected abstract function getExpectedFieldId();

    protected abstract function getExpectedUntranslatedFieldLabel();

    protected abstract function getExpectedUntranslatedFieldDescription();
}
