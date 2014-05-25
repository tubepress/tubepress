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
 * @covers tubepress_core_impl_options_ui_BaseElement
 */
abstract class tubepress_test_core_impl_options_ui_AbstractOptionsPageItemTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_options_ui_BaseElement
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    public final function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_core_api_translation_TranslatorInterface::_);

        $this->onOptionsPageItemSetup();

        $this->_sut = $this->buildSut();

    }

    public function testGetId()
    {
        $this->assertEquals($this->getOptionsPageItemId(), $this->_sut->getId());
    }

    protected function expectTranslation($incoming, $outgoing)
    {
        $this->_mockTranslator->shouldReceive('_')->once()->with($incoming)->andReturn($outgoing);
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockTranslator()
    {
        return $this->_mockTranslator;
    }

    protected function onOptionsPageItemSetup()
    {
        //override point
    }

    /**
     * @return tubepress_core_impl_options_ui_BaseElement
     */
    protected function getSut()
    {
        return $this->_sut;
    }

    /**
     * @return string
     */
    protected abstract function getOptionsPageItemId();

    /**
     * @return tubepress_core_impl_options_ui_BaseElement
     */
    protected abstract function buildSut();
}